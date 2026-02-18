<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('home_page.php');
    }

    public function test()
    {
        return view('tes.php');
    }
}


?>