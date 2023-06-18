<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

  'store' => [
    'name' => ['max' => 50],
    'url_name' => ['max' => 50],
  ],

  'user' => [
    'login_id' => [
      'min' => 4,
      'max' => 14
    ],
    'login_password' => [
      'min' => 8,
      'max' => 20
    ],
  ],
];