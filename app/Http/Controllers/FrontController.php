<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Event;
use JWTAuth;

class FrontController extends Controller
{

    public function __constructor()
    {
        $this->middleware('jwt.auth', ['only' => ['events']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return null;
    }

    public function recentEvents()
    {
        //fetch 5 events from database which are active and latest
        $events = Event::where('active', 1)->orderBy('created_at')->paginate(2);
        // page Heading
        $title = 'Latest Events';

        return view('home')->withEvents($events)->withTitle($title);
    }

    public function show($id)
    {
        $event = Event::where('id',$id)->first();
        if (!$event) {
            return redirect('/')->withErrors('requested page not found');
        }

        $comments = $event->comments;

        return view('events.show')->withEvent($event)->withComments($comments);
    }
}
