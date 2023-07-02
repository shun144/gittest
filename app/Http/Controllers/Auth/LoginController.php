<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // 認証対象カラムの変更
    // AuthenticatesUsersのusernameメソッドをオーバーライド
    public function username()
    {
      return 'login_id';
    }


    // 未ログインからのログイン後の遷移先を変更する
    public function redirectTo()
    { 
      if (Gate::allows('isAdmin')){
        return route('admin.store');
      }      
      else {
        return route('owner.schedule');
      }
    }
}
