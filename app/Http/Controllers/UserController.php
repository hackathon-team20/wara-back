<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostStoreRequest;
use App\Models\Post;
use App\Models\Review;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //投稿一覧を取得
        $query = Post::query();
        $query = $query->orderBy('created_at', 'desc')->with(['user'])->get();

        return response()->json(
            [
                'post' => $query,
            ],
            200
        );
    }

    public function ranking()
    {
        //TODO:いいね総獲得数が多い順でユーザー情報を取得、ユーザー情報といいね数も一緒に返す
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
        if (! is_numeric($id) || $id <= 0) {
            return response()->json(
                [
                    'code' => Response::HTTP_BAD_REQUEST,
                    'message' => 'Invalid ID',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
        $query = Post::query();
        $post = $query->with(['user','topic'])->find($id);

        if (! $post) {
            return response()->json(
                [
                    'code' => Response::HTTP_NOT_FOUND,
                    'message' => 'Post not found',
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json(
            [
                'post' => $post,
            ], 200
        );

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

    public function review(Request $request, string $id)
    {
        //ログイン中のユーザーが任意の投稿にハートを押した時、Reviewsテーブルにレコードを作成
        //TODO:いいねといいね解除がフロント側のボタン切り替えなどででできるのか相談する
        //できなかった場合はいいね解除されない限り１回までしか実行出来ないようにしないとまずそう
        if (! is_numeric($id) || $id <= 0) {
            return response()->json(
                [
                    'code' => Response::HTTP_BAD_REQUEST,
                    'message' => 'Invalid ID',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
        //reviewメソッドの第二引数を投稿のidとして受け取って、post_idカラムにだけは個別で値を挿入
        $reviewData = $request->all();
        $reviewData['post_id'] = $id;

        //レビューの作成
        //作成時にログイン中のユーザーのidが、user_idカラムに挿入されるようにReviewモデルで実装済み
        $review = Review::create($reviewData);


        return response()->json(
            [
                'message' => 'postData created successfully!',
                'review' => $review,
            ],
            200
        );
    }

    public function destroyReview(string $id)
    {
        //レビューの削除処理
        if (! is_numeric($id) || $id <= 0) {
            return response()->json(
                [
                    'code' => Response::HTTP_BAD_REQUEST,
                    'message' => 'Invalid ID',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
        //ログインしているユーザーのIdを取得
        $loginUserId = User::find(auth()->id())->id;
      
        $query = Review::query();
        $review = $query
            ->where('user_id', $loginUserId)
                ->where('post_id', $id)->first(); //delete()を使うために１つのレコードを取得

        if (! $review) {
            return response()->json(
                [
                    'id' => $loginUserId,
                    'code' => Response::HTTP_NOT_FOUND,
                    'message' => 'Review not found',
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        $review->delete();

        return response()->json(
            [
                'message' => 'Review deleted successfully!',
                'post' => $review,
            ]
          );
    }
}

