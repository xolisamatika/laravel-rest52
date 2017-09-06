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
            'venue' => 'required',
            'time' => 'required|date_format:YmdHie'
        ]);

        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['msg' => 'User not found'], 404);
        }

        $title = $request->input('title');
        $description = $request->input('description');
        $venue = $request->input('venue');
        $time = $request->input('time');
        $user_id = $user->id;

        $event = new Event([
            'title' => $title,
            'description' => $description,
            'time' => Carbon::createFromFormat('YmdHie', $time),
            'venue' => $venue
        ]);

        if ($user->events()->save($event)) {
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
        $event = Event::with('comments')->where('id', $id)->firstOrFail();
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
            'venue' => 'required',
            'time' => 'required|date_format:YmdHie'
        ]);

        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['msg' => 'User not authenticated'], 404);
        }
        $title = $request->input('title');
        $description = $request->input('description');
        $venue = $request->input('venue');
        $price = $request->input('price');
        $time = $request->input('time');

        $event = Event::with('comments')->findOrfail($id);
        if ($event->admin_id != $user->id) {
            return response()->json([
                            'msg' => 'Only the admin can delete the event, deletion not successfull',
                            401
                        ]);
        }

        $event->time = Carbon::createFromFormat('YmdHie', $time);
        $event->title = $title;
        $event->description = $description;
        $event->venue = $venue;
        $event->price = $price;

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
        if ($event->admin_id != $user->id) {
            return response()->json([
                            'msg' => 'Only the admin can delete the event, deletion not successfull',
                            401
                        ]);
        }
        $comments = $event->comments;
        $event->comments()->detach();
        if (!$event->delete()) {
            foreach ($comments as $comment) {
                $event->comments()->attach($comment);
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
