<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['topic_id', 'user_id', 'post_content', ];

    protected $dates = ['created_at', 'updated_at'];

}
