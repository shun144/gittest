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


    // public function render($request, Throwable $e) {

    //     if ($e instanceof TokenMismatchException) {
    //         if (url()->current() == route('logout')) {
    //             return redirect()->route('login');
    //         }
    //     }
    //     return parent::render($request, $e);
    // }

    public function render($request, Throwable $e)
    {
        if ($e instanceof TokenMismatchException) {
            \Log::info('★トークンミスマッチ!');
            return redirect()->route('login');
        }
        return parent::render($request, $e);
    }
}
