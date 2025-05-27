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
        return $this->belongsTo('App\Models\Users', 'user_id', 'id');
    }

    public function teacher() {
        return $this->belongsTo('App\Models\Teachers', 'teacher_id', 'id');
    }

    public function content() {
        return $this->belongsTo('App\Models\Contents', 'content_id', 'id');
    }
}
