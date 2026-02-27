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

    public function stats(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $total     = Post::where('user_id', $userId)->count();
        $published = Post::where('user_id', $userId)->where('status', 'published')->count();
        $drafts    = Post::where('user_id', $userId)->where('status', 'draft')->count();

        $monthly = Post::where('user_id', $userId)
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(created_at, "%b") as month, COUNT(*) as count, MONTH(created_at) as month_num')
            ->groupBy('month', 'month_num')
            ->orderBy('month_num')
            ->get()
            ->map(fn($item) => [
                'month' => $item->month,
                'count' => $item->count,
            ]);

        return $this->success([
            'total'     => $total,
            'published' => $published,
            'drafts'    => $drafts,
            'monthly'   => $monthly,
        ], 'Stats retrieved successfully');
    }

   public function index(Request $request): JsonResponse
    {
        $query = Post::where('user_id', $request->user()->id);

        // Search by title, body & status
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title',  'like', "%{$search}%")
                ->orWhere('body', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $posts = $query->latest()->paginate(10);

        // ✅ Manually build flat predictable structure
        return $this->success([
            'data'         => PostResource::collection($posts)->resolve(),
            'total'        => $posts->total(),
            'per_page'     => $posts->perPage(),
            'current_page' => $posts->currentPage(),
            'last_page'    => $posts->lastPage(),
            'from'         => $posts->firstItem(),
            'to'           => $posts->lastItem(),
        ], 'Posts retrieved successfully');
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
