<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Event;
use Carbon\Carbon;
use JWTAuth;

class EventController extends Controller
{
    public function __constructor()
    {
        $this->middleware('jwt.auth', ['only' => ['update', 'store', 'destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = Event::all();

        foreach ($events as $event) {
            $event->view_event = [
                'href' => 'api/v1/event/' . $event->id,
                'method' => 'GET'
            ];
        }


        $response = [
            'msg' => 'List of all Events',
            'events' => $events
        ];

        return response()->json($response, 200);
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
            'title' => 'required',
            'description' => 'required',
            'time' => 'required|date_format:YmdHie'
        ]);

        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['msg' => 'User not found'], 404);
        }

        $title = $request->input('title');
        $description = $request->input('description');
        $time = $request->input('time');
        $user_id = $user->id;

        $event = new Event([
            'title' => $title,
            'description' => $description,
            'time' => Carbon::createFromFormat('YmdHie', $time)
        ]);

        if ($event->save()) {
            $event->users()->attach($user_id);
            $event->view_event = [
                'href' => 'api/v1/event/' . $event->id,
                'method' => 'GET'
            ];
        }

        $response = [
            'msg' => 'Event created',
            'event' => $event
        ];

        return response()->json($response, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = Event::with('users')->where('id', $id)->firstOrFail();
        $event->view_event = [
            'href' => 'api/v1/event/' . $event->id,
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Event Information',
            'event' => $event
        ];

        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'time' => 'required|date_format:YmdHie'
        ]);

        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['msg' => 'User not found'], 404);
        }
        $title = $request->input('title');
        $description = $request->input('description');
        $time = $request->input('time');
        $user_id = $user->id;

        $event = Event::with('users')->findOrfail($id);
        if (!$event->users()->where('users.id', $user_id)->first()) {
            return response()->json([
                            'msg' => 'user not registered fot event, update not successfull',
                            401
                        ]);
        }

        $event->time = Carbon::createFromFormat('YmdHie', $time);
        $event->title = $title;
        $event->description = $description;

        if (!$event->update()) {
             return response()->json([
                        'msg' => 'Error during update',
                        404
                    ]);
        }

        $event->view_event = [
            'href' => 'api/v1/event/' . $event->id,
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Event updated',
            'event' => $event
        ];

        return response()->json($response, 200);
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
        $users = $event->users;
        $event->users->detach();
        if (!$event->delete) {
            foreach ($users as $user) {
                $event->users()->attach($user);
            }
            return response()->json(['msg' => 'deletion failed'], 404);
        }
        $response = [
                'msg' => 'Event deleted',
                'create' => [
                    'href' => 'api/v1/event',
                    'method' => 'POST',
                    'params' => 'title, description, time'
                ]
            ];

        return response()->json($response, 200);
    }
}
