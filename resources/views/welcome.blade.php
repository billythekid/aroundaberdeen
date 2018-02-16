@extends('layouts.app')

@section('content')
  <h1>{{ config('app.name') }}</h1>

  <p>Under construction</p>
  @auth
    <p><a href="{{route('site.index')}}">Sites</a></p>
  @endauth

@endsection
