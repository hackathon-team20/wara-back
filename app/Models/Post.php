<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'post_id');
    }

    //Eloquentモデルのカスタムアクセサを利用し、Postモデルに属性を追加
    protected $appends = ['total_reviews'];

    //Postを取得した際のjsonレスポンスにそのPostについたReview総数も一緒に返す
    //このメソッド命名は重要。getで始まり最後にAttributeをつける。間には追加した属性名のキャメルケース
    public function getTotalReviewsAttribute() 
    {
        return $this->reviews()->count();
    }

    protected $fillable = ['topic_id', 'user_id', 'post_content', ];

    protected $dates = ['created_at', 'updated_at'];

}
