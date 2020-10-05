@extends('blog.layout')

@section('content')
    <ul>
        @foreach ($files as $file)
            <li>
                <a href="{{ route('blog.show', [
                    'username' => $username,
                    'filename' => $file->name,
                ]) }}">{{ $file->name }}</a>
            </li>
        @endforeach
    </ul>
@endsection
