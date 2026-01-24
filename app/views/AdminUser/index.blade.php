@extends('layouts.admin');

@section('title', $title);

@section('content')
    @foreach ($user as $items)
        <h1>{{ htmlspecialchars($items['name']) }}</h1>
    @endforeach
@endsection