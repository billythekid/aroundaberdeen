@extends('layouts.app')

@section('content')
  <h1>Your Sites</h1>
  @foreach($sites as $site)
    <p>
      <a href="{{ route('site.show', $site) }}">{{ $site->name }}</a>
    <form action="{{ route('site.destroy', $site) }}" method="POST">
      {{ method_field('DELETE') }}
      {{ csrf_field() }}
      <div class="form-group">
        <input class="form-control btn btn-danger" type="submit" value="Delete Site">
      </div>
    </form>
    </p>
  @endforeach

  <h2>Add a Site</h2>
  <form action="{{ route('site.store') }}" method="POST">
    {{ method_field('POST') }}
    {{ csrf_field() }}
    <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
      <label for="name">Name</label>
      <input id="name" name="name" class="form-control" type="text" placeholder="Name" value="{{ old('name') }}" maxlength="23">
    </div>
    <div class="form-group">
        <input class="form-control btn btn-primary" type="submit" value="Save Site">
    </div>
  </form>
@endsection

@section('scripts')

@endsection