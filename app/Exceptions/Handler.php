<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];


    // セッションが切れている状態でログアウトを実行した際に
    // ログイン画面に遷移させる
    // これがないと419エラー画面となる
    public function render($request, Throwable $e) {
        if ($e instanceof TokenMismatchException) {
            if (url()->current() == route('logout')) {
                return redirect()->route('login');
            }
        }
        return parent::render($request, $e);
    }
}
