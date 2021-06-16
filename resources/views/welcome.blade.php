@extends('layout')

@section('content')
    <form action="/" method="post">
        @csrf
        <button type="submit">Add Tenant</button>
    </form>
@endsection
