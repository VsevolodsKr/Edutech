<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Encryptable;

class Likes extends Model
{
    use HasFactory;
    use Encryptable;
    
    protected $fillable = [
        'user_id',
        'teacher_id',
        'content_id',
    ];

    protected $appends = ['encrypted_id'];

    public $timestamps = true;

    public function user() {
        return $this->hasOne('App\Models\Users', 'id', 'user_id');
    }

    public function teacher() {
        return $this->hasOne('App\Models\Teachers', 'id', 'teacher_id');
    }

    public function content() {
        return $this->hasOne('App\Models\Contents', 'id', 'content_id');
    }
}
