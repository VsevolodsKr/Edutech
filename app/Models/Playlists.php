<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlists extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    
    protected $fillable = [
        'teacher_id',
        'title',
        'description',
        'thumb',
        'date',
        'status',
    ];

    protected $appends = ['formatted_date'];

    public function getStatusAttribute($value)
    {
        // Return the actual status value from the database
        return $value;
    }

    public function getThumbAttribute($value)
    {
        if (!$value) {
            return null;
        }
        return str_replace('/storage/app/public/', '/storage/', $value);
    }

    public function getFormattedDateAttribute()
    {
        return $this->date ? date('F j, Y', strtotime($this->date)) : null;
    }

    public function teacher() {
        return $this->hasOne('App\Models\Teachers', 'id', 'teacher_id');
    }

    public function contents() {
        return $this->hasMany('App\Models\Contents', 'playlist_id', 'id');
    }
}
