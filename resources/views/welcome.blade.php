@extends('layouts.app')

@section('content')
  <div class="site-container">
    <h1 style="width: 100%;">{{ config('app.name') }}</h1>


    <div class="sites" style="width: 100%;">
      <hr>
      @foreach($sites as $site)
        <p>
          <a href="//{{ $site->subdomain }}.{{ env('APP_DOMAIN') }}">{{ $site->name }}</a> (by {{ $site->user->name }})
        </p>
      @endforeach
    </div>
  </div>
@endsection
