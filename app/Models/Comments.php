<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Encryptable;

class Comments extends Model
{
    use HasFactory;
    use Encryptable;
    
    protected $fillable = [
        'content_id',
        'user_id',
        'teacher_id',
        'comment',
        'date'
    ];

    protected $appends = ['encrypted_id'];

    public $timestamps = false;

    public function content() {
        return $this->belongsTo('App\Models\Contents', 'content_id', 'id');
    }

    public function user() {
        return $this->belongsTo('App\Models\Users', 'user_id', 'id');
    }

    public function teacher() {
        return $this->belongsTo('App\Models\Teachers', 'teacher_id', 'id');
    }
}
