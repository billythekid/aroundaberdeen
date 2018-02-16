@extends('layouts.app')

@section('content')
  {{-- This line just gets rid of the session issue with 404 pages. --}}
  @php $errors = new \Illuminate\Support\MessageBag([]); @endphp

  <h1>404</h1>
  <h2>{{ $exception->getMessage() }}</h2>
@endsection

@section('scripts')

@endsection