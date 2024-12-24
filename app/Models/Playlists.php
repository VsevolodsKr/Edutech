<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlists extends Model
{
    use HasFactory;
    protected $fillable = [
        'teacher_id',
        'title',
        'description',
        'thumb',
        'date',
        'status',
    ];
    public $timestamps = false;

    public function teacher() {
        return $this->hasOne('App\Models\Teachers', 'id', 'teacher_id');
    }
}
