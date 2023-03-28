<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Services\FileService;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostCollection;

class PostController extends Controller
{
  /**
   * Display a listing of the resource.
   */

  public function index()
  {
    try {
      $posts = Post::orderBy('created_at', 'desc')->get();
      return response()->json(new PostCollection($posts), 200);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 400);
    }
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    // Primero se tiene que aumentar upload_max_filesize & post_max_size (php.ini) para que recien tome la validación de max

    $request->validate([
      'video' => 'required|mimes:mp4|max:3072',
      'text' => 'required'
    ], [
      'video.max' => 'El size max must be 3M'
    ]);

    try {
      $post = new Post();
      $service = new FileService();
      $post = $service->addVideo($post, $request);

      $post->user_id = auth()->user()->id;
      $post->text = $request->input('text');
      $post->save();

      return response()->json(['success' => 'OK'], 200);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 400);
    }
  }

  /**
   * Display the specified resource.
   */
  public function show($id)
  {
    try {
      $post = Post::firstOrFail($id); //revisar
      $postsByUser = Post::where('user_id', $post->user_id)->get();

      $ids = $postsByUser->map(function ($post) {
        return $post->id;
      });

      return response()->json([
        'post' => new PostCollection($post),
        'ids' => $ids
      ], 200);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 400);
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy($id)
  {
    try {
      $post = Post::findOrFail($id);
      if (!is_null($post->video) && file_exists(public_path() . $post->video)) {
        unlink(public_path() . $post->video);
      }
      $post->delete();

      return response()->json(['success' => 'OK'], 200);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 400);
    }
  }
}
