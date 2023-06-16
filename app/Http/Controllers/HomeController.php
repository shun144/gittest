<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{

    public function viewLogin()
    {
        return redirect('/login');
    }
}
