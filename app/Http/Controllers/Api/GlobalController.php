<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Models\User;
use Illuminate\Http\Request;

class GlobalController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function getRandomUsers()
  {
    try {
      $suggested = User::inRandomOrder()->limit(5)->get();
      $following = User::inRandomOrder()->limit(10)->get();

      return response()->json([
        'suggested' => new UserCollection($suggested),
        'following' => new UserCollection($following),
      ]);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 400);
    }
  }
}
