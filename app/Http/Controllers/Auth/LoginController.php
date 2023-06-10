<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

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

    // ログイン後の遷移先を変更する
    public function redirectTo()
    {      
      $role = $this->guard()->user()->role;
      // dd($role);
      if ($role == 'admin')
      {        
        return route('admin.store');
      }
      else
      {
        return route('owner.schedule');
      }
    }
}
