@extends('layouts.app')

@section('content')
    <h1>Login</h1>

    <form class="form-horizontal" method="POST" action="{{ route('login') }}">
        {{ csrf_field() }}

        <label for="email">E-Mail Address</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        @if ($errors->has('email'))
            <span><strong>{{ $errors->first('email') }}</strong></span>
        @endif

        <label for="password">Password</label>
        <input id="password" type="password" class="form-control" name="password" required>
        @if ($errors->has('password'))
            <span><strong>{{ $errors->first('password') }}</strong></span>
        @endif

        <label>
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
        </label>

        <button type="submit" class="btn">
            Login
        </button>

        <a class="btn" href="{{ route('password.request') }}">
            Forgot Your Password?
        </a>

    </form>

@endsection
