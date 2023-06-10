<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Message;

class Image extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
