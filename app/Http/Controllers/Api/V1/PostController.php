<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Post\StorePostRequest;
use App\Http\Requests\Api\V1\Post\UpdatePostRequest;
use App\Http\Resources\Api\V1\PostResource;
use App\Models\Post;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $posts = Post::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return $this->success(
            PostResource::collection($posts)->response()->getData(true),
            'Posts retrieved successfully'
        );
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $post = Post::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return $this->success(new PostResource($post), 'Post created successfully', 201);
    }

    public function show(Request $request, Post $post): JsonResponse
    {
        $this->authorizePost($request, $post);

        return $this->success(new PostResource($post), 'Post retrieved successfully');
    }

    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        $this->authorizePost($request, $post);

        $post->update($request->validated());

        return $this->success(new PostResource($post), 'Post updated successfully');
    }

    public function destroy(Request $request, Post $post): JsonResponse
    {
        $this->authorizePost($request, $post);

        $post->delete();

        return $this->success(null, 'Post deleted successfully');
    }

    private function authorizePost(Request $request, Post $post): void
    {
        if ($post->user_id !== $request->user()->id) {
            abort(403, 'You are not authorized to access this post.');
        }
    }
}
