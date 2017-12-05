@extends('layouts.app')

@section('content')
    <h1>Reset Password</h1>

    <form class="form-horizontal" method="POST" action="{{ route('password.email') }}">
        {{ csrf_field() }}

        <label for="email" class="col-md-4 control-label">E-Mail Address</label>
        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

        @if ($errors->has('email'))
            <span><strong>{{ $errors->first('email') }}</strong></span>
        @endif

        <button type="submit" class="btn btn-primary">
            Send Password Reset Link
        </button>

    </form>

@endsection
