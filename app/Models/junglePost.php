<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class junglePost extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'blog_id',
        'uuid',
        'slug',
        'content',
        'excerpt',
        'status',
        'featured_image',
        'title',
        'tags'
    ];
}
