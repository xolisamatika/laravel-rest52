<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Event;
use Carbon\Carbon;
use JWTAuth;

class CommentController extends Controller
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
         $comments = Event::all();

        foreach ($comments as $event) {
            $event->view_event = [
                'href' => 'api/v1/event/' . $event->id,
                'method' => 'GET'
            ];
        }

        $response = [
            'msg' => 'List of all comments',
            'comments' => $comments
        ];

        return response()->json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input['from_user'] = $request->user()->id;
        $input['on_item'] = $request->input('item_id');
        $input['item_type'] = $request->input('item_type');
        $input['body'] = $request->input('body');
        $slug = $request->input('slug');

        Comment::create($input);

        return redirect($slug)->with('message', 'Comment published');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
