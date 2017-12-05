@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="panel panel-default">
            <div class="panel-heading">{{ config('app.name') }}</div>

            <div class="panel-body">
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                <p>Under construction</p>
            </div>
        </div>
    </div>

@endsection
