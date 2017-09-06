@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">All events</div>

                <div class="panel-body">
                    @foreach ($events as $event)

                        <h2>{{ $event->id }} {{ $event->title }} <small>{{ $event->likes()->count() }} <i class="fa fa-thumbs-up"></i></small></h2>

                        @foreach ($event->likes as $user)
                            {{ $user->id }} likes this !<br>
                        @endforeach

                        @if ($event->isLiked)
                            <a href="{{ route('event.like', $event->id) }}">Unlike this shit</a>
                        @else
                            <a href="{{ route('event.like', $event->id) }}">Like this awesome event!</a>
                        @endif
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>
@endsection