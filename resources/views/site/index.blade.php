@extends('layouts.app')

@section('content')
  <h1>Your Sites</h1>

  @foreach($sites as $site)
    <h2>{{ $site->name }}</h2>

    <form action="{{ route('site.destroy', $site) }}" method="POST">
      <div class="flex">
        <a href="{{ route('site.show', $site) }}" class="btn w-30">View {{ $site->name }}</a>
        <a href="{{ route('site.edit', $site) }}" class="btn w-30">Edit {{ $site->name }}</a>
        <button class="btn w-30" type="submit">Delete {{ $site->name }}</button>
      </div>
      {{ method_field('DELETE') }}
      {{ csrf_field() }}
    </form>
  @endforeach
  <hr>
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