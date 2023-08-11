<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [
  'owner' => [
    'top' => 'storage\owner',
    'image' => [
      'template' => 'storage/owner/image/template',
      'greeting' => 'storage/owner/image/greeting',
    ]
  ],

  'user' => [
    'top' => 'storage\user',
    'image' => [
      'entry' => 'storage\user\image\entry'
    ]
  ]
];
