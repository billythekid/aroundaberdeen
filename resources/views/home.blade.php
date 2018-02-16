@extends('layouts.app')

@section('content')
  <div>
    <p>
      Hello {{$user->firstName}}, you are logged in.
      <small>
        Not {{$user->firstName}}?
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          Logout
        </a>
      </small>
    </p>
    <h2>
      @admin
      Admin
      @endadmin
      Dashboard
    </h2>
  </div>

  @if (session('status'))
    <div class="alert alert-success">
      {{ session('status') }}
    </div>
  @endif

  <div class="flex">
    <div>
      <h3>Sites</h3>
      @foreach($user->sites as $site)
        <p><a href="{{route('site.show',$site)}}">{{$site->name}}</a></p>
      @endforeach

    </div>

    <div>
      <h3>Maps</h3>
      @foreach($user->maps as $map)
        <p><a href="{{route('map.show',$map)}}">{{$map->name}}</a></p>
      @endforeach
    </div>

    <div>
      <h3>Points of Interest</h3>
      @foreach($user->points as $point)
        <p><a href="{{route('point.show',$point)}}">{{$point->name}}</a></p>
      @endforeach
    </div>

  </div>





@endsection
