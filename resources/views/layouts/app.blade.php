<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{--<!-- CSRF Token -->--}}
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name') }}</title>

  {{--<!-- Styles -->--}}
  <link href="{{ asset('css/app.css') }}?v={{ now()->timestamp }}" rel="stylesheet">
</head>
<body>
  <div id="app">
    <div class="site-header">
      <a href="{{route('index')}}">Home</a>
      @guest
        <a href="{{ route('login') }}">Login</a>
        <a href="{{ route('register') }}">Register</a>
      @else
        {{ Auth::user()->name }}
        <a href="{{ route('site.index') }}">Your Sites</a>
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
          {{ csrf_field() }}
        </form>
      @endguest
    </div>
    <div class="flex">
      @include('flash::message')

      @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
      @endif
      @yield('content')
    </div>

  </div>

  {{--<!-- Scripts -->--}}
  <script src="{{ asset('js/app.js') }}"></script>

  @yield('scripts')
</body>
</html>
