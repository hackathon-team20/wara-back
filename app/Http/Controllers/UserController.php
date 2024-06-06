<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostStoreRequest;
use App\Models\Post;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //投稿一覧を取得
        $query = Post::query();
        $query = $query->orderBy('created_at', 'desc');
    }

    public function ranking()
    {
        //いいね総獲得数が多い順でユーザー情報を取得
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostStoreRequest $request)
    {
        //投稿を作成　(実際は管理者は投稿作成できなくて良さそうだけどテスト)
        $postData = Post::create($request->all());

        return response()->json(
            [
                'message' => 'postData created successfully!',
                'post' => $postData,
            ],
            200
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //一件の投稿の詳細情報を取得
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyPost(string $id)
    {
        //投稿の削除処理
        if (! is_numeric($id) || $id <= 0) {
            return response()->json(
                [
                    'code' => Response::HTTP_BAD_REQUEST,
                    'message' => 'Invalid ID',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $post = Post::find($id);

        if (! $post) {
            return response()->json(
                [
                    'code' => Response::HTTP_NOT_FOUND,
                    'message' => 'Post not found',
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        $post->delete();

        return response()->json(
            [
                'message' => 'Post deleted successfully!',
                'post' => $post,
            ],
            200
        );
    }
    public function mypage(){
        $userDate = User::find(auth()->id());

        if (!$userDate) {
            return response()->json(
                [
                    'error' => 'User not found',
                ],
                404
            );
        }

        return response()->json(
            [
                'user' => $userDate,
            ],
            200
        );
    }
    public function mypost(){
        $posts = Post::where('user_id', auth()->id())
                ->with('topic')
                ->get();

        return response()->json(
            [
                'my_posts' => $posts,
            ],
            200
        );
    }
}
