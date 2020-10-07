@extends('blog.layout')

@section('content')
    <h1>{{ $title }}</h1>
    <ul>
        @foreach ($files as $file)
            <li>
                <a href="{{ route('blog.show', [
                    'username' => $username,
                    'filename' => $file->name,
                ]) }}">{{ $file->created_at->toFormattedDateString() }} "{{ $file->name }}"</a>
            </li>
        @endforeach
    </ul>
@endsection
