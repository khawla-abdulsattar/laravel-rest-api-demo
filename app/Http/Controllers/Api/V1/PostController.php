<?php

// app/Http/Controllers/Api/V1/PostController.php

namespace App\Http\Controllers\Api\V1;
use App\Filters\PostFilter; // ✅ استدعاء الكلاس بشكل صحيح
use App\Http\Resources\PostResource;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Policies;


class PostController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $query = Post::with('user');

        // ✅ تطبيق الفلتر إذا كان موجودًا
        $posts = (new PostFilter($request))->apply($query)->latest()->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Posts retrieved successfully',
            'data' => PostResource::collection($posts),
        ], 200);
    }


    // إنشاء منشور جديد (المستخدم الحالي هو صاحب المنشور)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = auth()->user()->posts()->create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Post created successfully',
            'data' => new PostResource($post)
        ], 201);
    }


    public function show($id)
    {
        $post = Post::with('user')->find($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Post retrieved successfully',
            'data' => new PostResource($post),
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $this->authorize('update', $post); // التحقق من الصلاحية

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
        ]);

        $post->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Post updated successfully',
            'data' => new PostResource($post)
        ], 200);
    }
    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        // التحقق من أن المستخدم هو صاحب المنشور
        if (auth()->user()->id !== $post->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $post->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Post deleted successfully'
        ], 200);
    }
}
