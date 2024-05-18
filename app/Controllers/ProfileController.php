<?php

namespace App\Controllers;

use App\Components\Controller;
use App\Lib\Utils;

class ProfileController extends Controller
{
    public function index(int $id, string $username): void
    {
        $this->view('index', [
            'id' => $id,
            'username' => Utils::cleanup($username)
        ]);
    }

    public function settings(): void
    {
        $this->view('settings');
    }
}
