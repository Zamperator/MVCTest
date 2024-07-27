<?php

namespace App\Models;

use App\Lib\Exceptions\UserError;
use App\Components\Model;
use App\Lib\Utils;
use App\Lib\Registry;
use PDO;

class UserModel extends Model
{
    protected int $id;
    protected array $data;

    /**
     * @return void
     */
    public function init(): void
    {
    }

    /**
     * @param string $name
     * @param string $email
     * @param string $pass
     * @return int (user_id)
     */
    function create(string $name, string $email, string $pass): int
    {
        return 0;
    }

    /**
     * @return void
     */
    public function delete(): void
    {
    }

    /**
     * @param string $email
     * @return bool
     */
    function emailExists(string $email): bool
    {
        return false;
    }

    /**
     * @return void
     */
    public function load(): void
    {
    }

    /**
     * @param string $field
     * @return mixed|null
     */
    public function get(string $field): mixed
    {
        return $this->data[$field] ?? null;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->data;
    }

    private function loadData(string $field, $search, int $withStatus = -1): void
    {
    }

    /**
     * @param int $user_id
     * @param int $withStatus
     * @return void
     * @throws UserError
     */
    public function loadById(int $user_id, int $withStatus = -1): void
    {
        if (!$this->id) {
            $this->loadData('id', $user_id, $withStatus);
        }
    }

    /**
     * @param $email
     * @param int $withStatus
     * @return void
     * @throws UserError
     */
    public function loadByEmail($email, int $withStatus = -1): void
    {
        if (!$this->emailExists($email)) {
            throw new UserError("User not found");
        }

        if (!$this->id) {
            $this->loadData('email', $email, $withStatus);
        }
    }

    /**
     * @param string $session
     * @param int $withStatus
     * @return void
     * @throws UserError
     */
    public function loadBySession(string $session, int $withStatus = -1): void
    {
        if (!$this->id) {
            $this->loadData('session', $session, $withStatus);
        }
    }

    /**
     * @param string $pass
     * @return bool
     */
    public function validatePassword(string $pass): bool
    {
        return false;
    }

    /**
     * @param string $field
     * @param string $value
     * @return bool
     */
    public function update(string $field, string $value): bool
    {
        if (!$this->id) {
            return false;
        }

        return false;
    }
    /**
     * @return bool
     */
    public function loggedIn(): bool
    {
        return false;
    }
}