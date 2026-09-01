@extends('admin.layout')

@section('content')
<div class="toolbar">
    <div>
        <h1 style="margin:0 0 6px;">Редагування</h1>
        <div class="muted">{{ $product->name }}</div>
    </div>
</div>

<form method="POST"
      enctype="multipart/form-data"
      action="{{ route('admin.products.update', $product) }}">
    @csrf
    @method('PUT')
    @include('admin.products._form')
</form>
@endsection
