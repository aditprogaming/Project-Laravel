<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'profession',
        'about',
        'skills',
        'experience',
        'contact',
        'template',
        'photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
