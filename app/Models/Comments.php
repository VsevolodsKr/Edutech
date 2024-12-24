<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    use HasFactory;
    protected $fillable = [
        'content_id',
        'user_id',
        'teacher_id',
        'comment',
        'date'
    ];

    public function content() {
        return $this->hasOne('App\Models\Contents', 'id', 'content_id');
    }

    public function user() {
        return $this->hasOne('App\Models\Users', 'id', 'user_id');
    }

    public function teacher() {
        return $this->hasOne('App\Models\Teachers', 'id', 'teacher_id');
    }
}
