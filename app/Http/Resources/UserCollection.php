<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
  /**
   * Transform the resource collection into an array.
   *
   * @return array<int|string, mixed>
   */
  public function toArray(Request $request)
  {
    return $this->collection->map(function ($user) {
      return [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'bio' => $user->bio,
        'image' => url('/') . $user->image,
        'created_at' => url('/') . $user->created_at,
        'updated_at' => url('/') . $user->updated_at,
      ];
    });
  }
}
