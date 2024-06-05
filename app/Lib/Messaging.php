<?php

/***********************************************************************************************************************
 * Messaging class with RabbitMQ
 *
 * @Example
 * $messaging = new Messaging();
 *
 * function process_message($message) {
 *     echo "Processing message: $message\n";
 * }
 *
 * @Send
 * $messaging->send('hello', 'Hello World!');
 *
 * @Receive
 * $messaging->receive('hello', 'process_message');
 *
 * @HINT Make sure to have the RabbitMQ server running and do NOT use it directly in the environment. (Loop)
 *
 *********************************************************************************************************************/


namespace App\Lib;

use Exception;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * @uses PhpAmqpLib
 * @package App\Lib
 * @version 1.0
 * @since 1.0
 * @see https://www.rabbitmq.com/tutorials/tutorial-one-php.html
 */
class Messaging
{

    protected AMQPStreamConnection $connection;
    protected AMQPChannel $channel;
    protected bool $active = false;


    /**
     * @throws Exception
     */
    public function __construct()
    {
        $config = Registry::get('config')['rabbitmq'];

        if (!$config['active']) {
            return;
        }

        $this->connection = new AMQPStreamConnection(
            $config['host'],
            $config['requestPort'],
            $config['user'],
            $config['password']
        );

        $this->channel = $this->connection->channel();

        $this->active = true;
    }

    /**
     * @throws Exception
     */
    public function __destruct()
    {
        if ($this->active) {
            $this->channel->close();
            $this->connection->close();
        }
    }

    /**
     * @param string $queue
     * @return string
     */
    public function prepareQueue(string $queue): string
    {
        return trim(preg_replace('#[^a-z_-]#i', '', $queue));
    }

    /**
     * @param string $message
     * @return string
     */
    public function prepareMessage(string $message): string
    {
        return Utils::cleanup($message);
    }


    /**
     * @param string $queue
     * @param string $message
     * @return void
     */
    public function send(string $queue, string $message): void
    {
        if (!$this->active) {
            return;
        }

        $queue = $this->prepareQueue($queue);
        $message = $this->prepareMessage($message);

        // Sicherstellen, dass die Warteschlange existiert
        $this->channel->queue_declare($queue, false, true, false, false);

        // Nachricht senden
        $msg = new AMQPMessage($message, ['delivery_mode' => 2]); // make message persistent
        $this->channel->basic_publish($msg, '', $queue);

        echo " [x] Sent $message\n";
    }

    /**
     * @param string $queue
     * @param callable $callback
     * @return void
     */
    public function receive(string $queue, callable $callback): void
    {
        if (!$this->active) {
            return;
        }

        $queue = $this->prepareQueue($queue);

        // Sicherstellen, dass die Warteschlange existiert
        $this->channel->queue_declare($queue, false, true, false, false);

        $callable = function ($msg) use ($callback) {
            echo " [x] Received $msg->body\n";
            call_user_func($callback, $msg->body);
            $msg->ack();
        };

        // Nachricht empfangen
        $this->channel->basic_consume($queue, '', false, false, false, false, $callable);

        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }
}
