<?php

namespace App\Http\Controllers;

use App\Http\Requests\TopicStoreRequest;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexPost()
    {
        //管理者が投稿を閲覧できる
        $query = Post::query();
        $result = $query->paginate(10);

        return response()->json([
            'posts' => $result->items(),
            'meta' => [
                'currentPage' => $result->currentPage(),
                'lastPage' => $result->lastPage(),
                'total' => $result->total(),
            ],
        ], 200);
    }

    public function indexTopic()
    {
        //管理者がお題を閲覧できる
        $query = Topic::query();
        $result = $query->paginate(10);

        return response()->json([
            'topics' => $result->items(),
            'meta' => [
                'currentPage' => $result->currentPage(),
                'lastPage' => $result->lastPage(),
                'total' => $result->total(),
            ],
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function storeTopic(TopicStoreRequest $request)
    {
        //投稿を作成　(実際は管理者は投稿作成できなくて良さそうだけどテスト)
        $TopicData = Topic::create($request->all());

        return response()->json(
            [
                'message' => 'TopicData created successfully!',
                'post' => $TopicData,
            ],
            200
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyPost(string $id)
    {
        //投稿の削除処理を実装
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

    public function destroyTopic(string $id)
    {
        //投稿の削除処理を実装
        if (! is_numeric($id) || $id <= 0) {
            return response()->json(
                [
                    'code' => Response::HTTP_BAD_REQUEST,
                    'message' => 'Invalid ID',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $topic = Topic::find($id);

        if (! $topic) {
            return response()->json(
                [
                    'code' => Response::HTTP_NOT_FOUND,
                    'message' => 'Post not found',
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        $topic->delete();

        return response()->json(
            [
                'message' => 'Topic deleted successfully!',
                'topic' => $topic,
            ],
            200
        );
    }
}
