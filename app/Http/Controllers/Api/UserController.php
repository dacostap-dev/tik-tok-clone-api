<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\FileService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\PostCollection;
use App\Http\Resources\UserCollection;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
  /**
   * Display a listing of the resource.
   */

  public function getAuthUser()
  {
    try {
      $user = User::where('id', auth()->user()->id)->get();
      return response()->json(new UserCollection($user), 200);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 400);
    }
  }

  /**
   * Request token to flutter App (Login)
   */

  public function requestToken(Request $request): string
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required',
      'device_name' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
      throw ValidationException::withMessages([
        'email' => ['The provided credentials are incorrect.'],
      ]);
    }

    return $user->createToken($request->device_name)->plainTextToken;
  }

  /**
   * Create user an d token to flutter App (Register)
   */

  public function createUserApp(Request $request)
  {
    $request->validate([
      'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
      'device_name' => 'required',
    ]);


    try {


      $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'image' => '/default-avatar.jpeg',
        'bio' => $request->bio,
        'password' => Hash::make($request->password),
      ]);

      return $user->createToken($request->device_name)->plainTextToken;
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 400);
    }
  }


  /**
   * Request to delete token to flutter App (Logout)
   */

  public function deleteToken(Request $request): string
  {
    return  $request->user()->currentAccessToken()->delete();
  }

  /**
   * Update a newly image in storage.
   */
  public function updateUserImage(Request $request)
  {

    $request->validate(['image' => 'required | mimes:png,jpg,jpeg']);

    if ($request->height == '' || $request->width == '' || $request->top == '' || $request->left == '') {
      return response()->json(['error' => 'The dimensions are imcomplete'], 400);
    }

    try {
      $service = new FileService;
      $user = $service->updateImage(auth()->user(), $request);
      $user->save();

      return response()->json(['success' => 'Ok'], 200);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 400);
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    try {
      $user = User::where('id', $id)->get();
      $posts = Post::where('user_id', $id)->orderBy('created_at', 'desc')->get();

      return response()->json([
        'user' => new UserCollection($user),
        'posts' => new PostCollection($posts),
      ], 200);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 400);
    }
  }



  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request)
  {
    $request->validate(['name' => 'required']);

    try {
      $user = User::findOrFail(auth()->user()->id);

      $user->name = $request->input('name');
      $user->bio = $request->input('bio');
      $user->save();

      return response()->json(['success' => 'Ok'], 200);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 400);
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    //
  }
}
