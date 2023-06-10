<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Schedule;
use \GuzzleHttp\Client;
use App\Models\Line;
use App\Models\Store;


class HomeController extends Controller
{

    public function showLogin()
    {
        return redirect('/login');
    }

    // /**
    //  * Create a new controller instance.
    //  *
    //  * @return void
    //  */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    // public function index()
    // {
    //     return view('home');
    // }

    // public function store()
    // {
    //     $stores = Store::select('stores.*')
    //     ->get();

    //     return view('mypage.store', compact('stores'));
    // }
}
