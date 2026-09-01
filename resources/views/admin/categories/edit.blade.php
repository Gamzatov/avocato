@extends('admin.layout')

@section('content')
<div class="toolbar">
    <div>
        <h1 style="margin:0 0 6px;">Редагування фільтра</h1>
        <div class="muted">{{ $category->name }}</div>
    </div>
</div>

<form method="POST" action="{{ route('admin.categories.update', $category) }}">
    @csrf
    @method('PUT')
    @include('admin.categories._form')
</form>
@endsection
