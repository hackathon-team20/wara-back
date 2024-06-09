<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostStoreRequest;
use App\Models\Post;
use App\Models\Review;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexPosts()
    {
        //ログイン中のユーザーを取得
        $userId = auth()->id();

        // 最新のトピックを取得
        $topic = Topic::orderBy('created_at', 'desc')->first();

        // 最新のトピックに紐づく投稿一覧を取得
        $posts = Post::where('topic_id', $topic->id)
        ->with(['user', 'reviews'])
        ->orderBy('created_at', 'desc')
        ->get();

        //各投稿に対してログインユーザーがレビューをつけているかを確認
        $query->each(function ($query) use ($userId) {
        $query->isReviewedByUser = $query->reviews->where('user_id', $userId)->isNotEmpty();
        //reviewsコレクションをメモリから解放する
        unset($query->reviews);
    });

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
        $users = User::select('users.*', DB::raw('COUNT(reviews.id) as user_total_hearts'))
            ->leftJoin('posts', 'posts.user_id', '=', 'users.id')
            ->leftJoin('reviews', 'reviews.post_id', '=', 'posts.id')
            ->groupBy('users.id')
            ->orderBy('user_total_hearts', 'desc')
            ->get();

            return response()->json(
                [
                    'users' => $users,
                ],
                200
            );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostStoreRequest $request, string $id)
    {
        //投稿を作成
        $data = $request->all();

        $data['topic_id'] = $id;
        $data['user_id'] = auth()->id();

        $postData = Post::create($data);

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
        // userId に基づいてユーザー情報を取得、同時に投稿とトピックも取得
        $user = User::with(['posts.topic'])->find(auth()->id());
    
        // ユーザーが存在しない場合のエラーハンドリング（念のため）
        if (!$user) {
            return response()->json([
                'error' => 'User not found',
            ], 404);
        }
    
        return response()->json(
            [
                'user' => $user,
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
                'posts' => $posts,
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
                'message' => 'Review created successfully!',
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
                'review' => $review,
            ]
        );
    }

    public function otheruser(string $id)
    {
        // userId に基づいてユーザー情報を取得、同時に投稿とトピックも取得
        $user = User::with(['posts.topic'])->find($id);
    
        // ユーザーが存在しない場合のエラーハンドリング（念のため）
        if (!$user) {
            return response()->json([
                'error' => 'User not found',
            ], 404);
        }
    
        return response()->json(
            [
                'user' => $user,
            ],
            200
        );
    }
    public function otheruserPosts(string $id)
    {
        // 指定されたユーザーIDに基づいて投稿を取得
        $posts = Post::where('user_id', $id)
            ->with('topic')
            ->get();

        // 投稿情報を JSON レスポンスとして返す
        return response()->json(
            [
                'posts' => $posts,
            ],
            200
        );
    }
    public function recent()
    {
    // ログイン中のユーザー情報を取得
    $userId = auth()->id();

    // 最新のトピックを取得
    $topic = Topic::orderBy('created_at', 'desc')->first();

    // ユーザーが最新のトピックに対して投稿しているかを確認
    $post = Post::where('topic_id',$topic->id)
        ->where('user_id', $userId)
        ->first();

    // レスポンスを返す
    if (!$post) {
        return response()->json([
            'topic_id' => $topic->id,
            'topic' => $topic->topic,
            'topic_image' => $topic->image,
            'topic_created_at' => $topic->created_at,
            'post_id' => null,
            'post_content' => null,
            'post_created_at' =>  null
        ], 200);
    }

    // ユーザーが最新のトピックに対して投稿している場合、トピックと投稿内容を返す
    return response()->json([
        'topic_id' => $topic->id,
        'topic' => $topic->topic,
        'topic_image' => $topic->image,
        'topic_created_at' => $topic->created_at,
        'post_id' => $post->id,
        'post_content' => $post->post_content,
        'post_created_at' =>  $post->created_at
    ], 200);
    }

    public function showTopic(string $id)
    {
        // 指定されたトピックIDに基づいて投稿を取得
        $posts = Post::where('topic_id', $id)
            ->with('topic')
            ->get();

        // 投稿情報を JSON レスポンスとして返す
        return response()->json(
            [
                'posts' => $posts,
            ],
            200
        );
    }
}

