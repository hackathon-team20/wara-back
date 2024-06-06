<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Review extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
    protected $fillable = ['user_id', 'post_id', ];

    protected $dates = ['created_at', 'updated_at'];

    public static function boot()
    {
        parent::boot();
        
        //ログイン中のユーザーがreviewを作成した際に、そのユーザーのidがuser_idカラムに挿入される処理
        static::creating(function ($review) {
            $review->user_id = Auth::id();
        });
    }

}
