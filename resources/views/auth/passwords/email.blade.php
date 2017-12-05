@extends('layouts.app')

@section('content')
    <h1>Reset Password</h1>

    <form method="POST" action="{{ route('password.email') }}">
        {{ csrf_field() }}

        <label for="email">E-Mail Address</label>
        <input id="email" type="email"name="email" value="{{ old('email') }}" required>

        @if ($errors->has('email'))
            <span><strong>{{ $errors->first('email') }}</strong></span>
        @endif

        <button type="submit" class="btn">
            Send Password Reset Link
        </button>

    </form>

@endsection
