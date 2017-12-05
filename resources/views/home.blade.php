@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="panel panel-default">
            <div class="panel-heading">Dashboard</div>

            <div class="panel-body">
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <p>You are logged in! There's nothing to do here just now though.</p>
            </div>
        </div>
    </div>

@endsection
