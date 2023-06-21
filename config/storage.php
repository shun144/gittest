<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [
  'owner' => [
    'top' => 'storage\owner',
    'image' => [
      'template' => 'storage/owner/image/template'
      // 'template' => 'storage\owner\image\template'
    ]
  ],

  'user' => [
    'top' => 'storage\user',
    'image' => [
      'register' => 'storage\user\image\register'
    ]
  ]




    // 'image' => [
    //     'owner' => [
    //         'template' => 'storage\owner\template',
    //     ],
    //     'user' => [
    //         'register' => 'storage\user\register'
    //     ]
    // ],
];
