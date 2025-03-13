<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'thumbnail', 'priority', 'status'];

    
    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail
            ? asset($this->thumbnail)
            : asset('rules/images/default.png');
    }
    
    

    

}
