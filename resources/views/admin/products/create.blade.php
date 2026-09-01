@extends('admin.layout')

@section('content')
<div class="toolbar">
    <div>
        <h1 style="margin:0 0 6px;">Новий продукт</h1>
        <div class="muted">Створення нової позиції меню.</div>
    </div>
</div>

<form method="POST"
      enctype="multipart/form-data"
      action="{{ route('admin.products.store') }}">
    @csrf
    @include('admin.products._form')
</form>
@endsection
