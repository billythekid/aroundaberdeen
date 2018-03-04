@extends('layouts.app')

@section('content')
  <h1>{{ config('app.name') }}</h1>

  @foreach($sites as $site)
    <p>
      <a href="//{{ $site->subdomain }}.{{ env('APP_DOMAIN') }}">{{ $site->name }}</a>
    </p>
  @endforeach

@endsection
