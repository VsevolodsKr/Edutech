<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;
    protected $fillable = [
        'teacher_id',
        'playlist_id',
        'title',
        'description',
        'video',
        'thumb',
        'date',
        'status',
    ];
}
