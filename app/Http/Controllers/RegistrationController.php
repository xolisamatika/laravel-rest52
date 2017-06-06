<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Event;
use App\User;
use JWTAuth;

class RegistrationController extends Controller
{
    public function __constructor()
    {
        $this->middleware('jwt.auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'event_id' => 'required',
            'user_id' => 'required'
        ]);

        $event_id = $request->input('event_id');
        $user_id = $request->input('user_id');

        $event = Event::findOrFail($event_id);
        $user = User::findOrfail($user_id);

        $message = [
            'msg' => 'User is already registered for event',
            'event' => $event,
            'user' => $user,
            'unregister' => [
                'href' => 'api/v1/event/registration/' . $event->id,
                'method' => 'DELETE'
            ]
        ];

        if ($event->users()->where('users.id', $user_id)->first()) {
             return response()->json($message, 404);
        }
        $user->events()->attach($event);

        $response = [
            'msg' => 'User registered for event',
            'event' => $event,
            'user' => $user,
            'unregister' => [
                'href' => 'api/v1/event/registration/' . $event->id,
                'method' => 'DELETE'
            ]
        ];

         return response()->json($response, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['msg' => 'User not found'], 404);
        }
        if (!$event->users()->where('users.id', $user->id)->first()) {
            return response()->json([
                            'msg' => 'user not registered fot event, deletion not successfull',
                            401
                        ]);
        }
        $event->users()->detach($user->id);
        $response = [
            'msg' => 'User unregistered for event',
            'event' => $event,
            'user' => $user,
            'register' => [
                'href' => 'api/v1/event/registration',
                'method' => 'POST',
                'params' => 'user_id, event_id'
            ]
        ];

         return response()->json($response, 200);
    }
}
