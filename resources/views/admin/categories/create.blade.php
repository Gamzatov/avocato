@extends('admin.layout')

@section('content')
<div class="toolbar">
    <div>
        <h1 style="margin:0 0 6px;">Новий фільтр</h1>
        <div class="muted">Створення нової категорії меню.</div>
    </div>
</div>

<form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
    @csrf
    @include('admin.categories._form')
</form>
@endsection
