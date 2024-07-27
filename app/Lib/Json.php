<?php

namespace App\Lib;

final class Json
{
    function __construct()
    {
    }

    /**
     * @param array $data
     * @return void
     */
    public function view(array $data = []): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * @param array $data
     * @return string
     */
    public function get(array $data = []): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param string $error
     * @param string $message
     * @param $details
     * @param int $code
     * @param string $path
     * @return void
     */
    public function error(
        string $error = 'Unknown error',
        string $message = '',
               $details = null,
        int    $code = 0,
        string $path = ''
    ): void
    {
        $result = [
            'timestamp' => date('r', time()),
        ];

        if (!empty($code)) {
            $result['code'] = $code;
        }

        if (!empty($error)) {
            $result['error'] = $error;
        }

        if (!empty($message)) {
            $result['message'] = $message;
        }

        if (!empty($details)) {
            $result['details'] = $details;
        }

        if (!empty($path)) {
            $result['path'] = Utils::cleanup($path, 'string');
        }

        if (in_array($code, [404, 500])) {
            http_response_code($code);
        }

        $this->view($result);
    }
}