<?php

namespace App\Http\Controllers;

use App\Http\Requests\TopicStoreRequest;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\Request;

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

    public function indexTpic()
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
    public function destroy(string $id)
    {
        //
    }
}
