<?php

namespace App\Controllers;

use App\Components\Controller;
use App\Lib\Utils;

class IndexController extends Controller
{
    public function index(): void
    {
        // TODO: Remove - This is not gdpr compliant
        $this->pageSetup->addScriptFile('https://code.jquery.com/jquery-3.7.1.min.js', '3.7.1');
        $this->pageSetup->addScriptFile('/css/styles.css', '1');

        $this->pageSetup->addPageTitle('TEST');
        $this->pageSetup->addPageTitle('TEST2');

        $this->view('index', [
            'myIpAddress' => Utils::getClientIP()
        ]);
    }

    public function about(): void
    {
        $this->view('about');
    }

}
