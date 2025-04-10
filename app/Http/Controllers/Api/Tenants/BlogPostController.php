<?php

namespace App\Http\Controllers\Api\Tenants;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class BlogPostController extends Controller
{
    // Create a new blog post
    /**
     * @OA\Post(
     *     path="/api/blog-posts",
     *     tags={"BlogPosts"},
     *     summary="Create a new blog post",
     *     description="Create a new blog post for the authenticated user",
     *     operationId="createBlogPost",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Blog post details",
     *         @OA\JsonContent(
     *             required={"title", "content"},
     *             @OA\Property(property="title", type="string", example="My Blog Post"),
     *             @OA\Property(property="content", type="string", example="This is the content of the blog post")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Blog post created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Blog post created successfully"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $blogPost = BlogPost::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => Auth::id(),  // Link to the authenticated user
            'tenant_id' => tenant('id'),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Blog post created successfully',
            'data' => $blogPost,
        ], 201);
    }

    // Get all blog posts
    /**
     * @OA\Get(
     *     path="/api/blog-posts",
     *     tags={"BlogPosts"},
     *     summary="Get all blog posts by the authenticated user or tenant",
     *     description="Fetch all blog posts created by the authenticated user. If the user is not authenticated, fetch public blog posts for the tenant.",
     *     operationId="getAllBlogPosts",
     *     @OA\Response(
     *         response=200,
     *         description="List of blog posts by the authenticated user or public blog posts for the tenant",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="content", type="string"),
     *                     @OA\Property(property="user_id", type="integer"),
     *                     @OA\Property(property="tenant_id", type="string"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No blog posts found"
     *     )
     * )
     */
    public function index()
    {
        $user = Auth::user();

        if ($user) {
            // Authenticated user: Fetch posts by the authenticated user and the tenant
            $posts = BlogPost::where('user_id', $user->id)
                ->where('tenant_id', tenant('id'))
                ->get();
        } else {
            // Guest user: Fetch posts for the tenant (public posts)
            $posts = BlogPost::where('tenant_id', tenant('id'))->get();
        }

        return response()->json([
            'status' => true,
            'data' => $posts,
        ]);
    }


    // Get a specific blog post
    /**
     * @OA\Get(
     *     path="/api/blog-posts/{id}",
     *     tags={"BlogPosts"},
     *     summary="Get a specific blog post",
     *     description="Fetch a specific blog post by ID",
     *     operationId="getBlogPost",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the blog post to fetch",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Blog post details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="content", type="string"),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="tenant_id", type="string"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Blog post not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Blog post not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred while fetching the blog post"),
     *             @OA\Property(property="error", type="string", example="Error message details")
     *         )
     *     )
     * )
     */

    public function show($id)
    {
        try {
            $user = Auth::user();

            // Fetch the blog post based on the user authentication status
            if ($user) {
                // Authenticated user: Fetch posts by the authenticated user and the tenant
                $blogPost = BlogPost::where('id', $id)
                    ->where('user_id', $user->id)
                    ->where('tenant_id', tenant('id'))
                    ->first();
            } else {
                // Guest user: Fetch posts for the tenant (public posts)
                $blogPost = BlogPost::where('id', $id)
                    ->where('tenant_id', tenant('id'))
                    ->first();
            }

            // Check if the blog post exists
            if (!$blogPost) {
                return response()->json([
                    'status' => false,
                    'message' => 'Blog post not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $blogPost,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching the blog post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    // Update a specific blog post
    /**
     * @OA\Put(
     *     path="/api/blog-posts/{id}",
     *     tags={"BlogPosts"},
     *     summary="Update a specific blog post",
     *     description="Update the blog post by ID",
     *     operationId="updateBlogPost",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the blog post to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated blog post details",
     *         @OA\JsonContent(
     *             required={"title", "content"},
     *             @OA\Property(property="title", type="string", example="Updated Blog Post"),
     *             @OA\Property(property="content", type="string", example="Updated content of the blog post")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Blog post updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Blog post updated successfully"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Blog post not found",
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 403);
        }
        // Ensure that the authenticated user is the owner of the blog post

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        $blogPost = BlogPost::where('id', $id)->where('user_id', $user->id)->where('tenant_id', tenant('id'))->first();

        $blogPost->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Blog post updated successfully',
            'data' => $blogPost,
        ]);
    }

    // Delete a specific blog post
    /**
     * @OA\Delete(
     *     path="/api/blog-posts/{id}",
     *     tags={"BlogPosts"},
     *     summary="Delete a specific blog post",
     *     description="Delete the blog post by ID",
     *     operationId="deleteBlogPost",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the blog post to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Blog post deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Blog post deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Blog post not found",
     *     )
     * )
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 403);
        }
        $blogPost = BlogPost::where('id', $id)->where('user_id', $user->id)->where('tenant_id', tenant('id'))->first();

        $blogPost->delete();

        return response()->json([
            'status' => true,
            'message' => 'Blog post deleted successfully',
        ]);
    }
}
