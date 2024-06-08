<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
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


    // 認証方法の変更
    // AuthenticatesUsersのvalidateLoginメソッドをオーバーライド
    protected function validateLogin(Request $request)
    {
      $request->validate([
          $this->username() => 'required|string',
          'password' => 'required|string',
          // 'conditioncheck' => 'required'
      ],
      [
        $this->username() => 'ログインIDは必須入力です。',
        'password.required' => 'パスワードは必須入力です。',
        // 'conditioncheck.required' => '利用規約の同意チェックは必須です。',
      ]);
    }



    // 未ログインからのログイン後の遷移先を変更する
    public function redirectTo()
    {      
      if (Gate::allows('isAdmin')){
        return route('admin.store');
      }      
      else {
        return route('owner.delivery');
        // return route('owner.schedule');
      }
    }

}
