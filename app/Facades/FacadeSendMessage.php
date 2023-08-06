<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class FacadeSendMessage extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'sendMessage';
    }
}
