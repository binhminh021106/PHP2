@extends('layouts.admin')

@section('title', $title)

@section('content')
    <form action="brand/update/{{ $brand['id'] }}">
        <label for="">Tên thương hiệu</label>
    </form>
@endsection