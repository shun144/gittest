<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Line;

class Store extends Model
{
    // use HasFactory, SoftDeletes;
    use HasFactory, SoftDeletes;

    // public function users(): HasMany
    // {
    //     return $this->hasMany(User::class);
    // }

    // public function lines(): HasMany
    // {
    //     return $this->hasMany(Line::class);
    // }


    // public static function booted(): void
    // {
    //     static::deleted(function ($store) {
    //         $store->users()->delete();
    //         $store->lines()->delete();
    //     });
    // }
}


