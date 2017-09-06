<?php

namespace App\Http\Controllers;

use App\Like;
use JWTAuth;

class LikeController extends Controller
{
    // public function __constructor()
    // {
    //     $this->middleware('jwt.auth');
    // }

    public function likeEvent($id)
    {
        $this->handleLike('App\Event', $id);
        return redirect()->back();
    }

    public function likeComment($id)
    {
        $this->handleLike('App\Comment', $id);
        return redirect()->back();
    }

    public function handleLike($type, $id)
    {
        $return = false;
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['msg' => 'User not found'], 404);
        }
        $existing_like = Like::withTrashed()->whereLikeableType($type)->whereLikeableId($id)->whereUserId($user->id)->first();

        if (is_null($existing_like)) {
            $return = Like::create([
                'user_id'       =>  $user->id,
                'likeable_id'   => $id,
                'likeable_type' => $type,
            ]);
        } else {
            if (is_null($existing_like->deleted_at)) {
                $return = !$existing_like->delete();
            } else {
                $return = $existing_like->restore();
            }
        }
        return $return;
    }
}
