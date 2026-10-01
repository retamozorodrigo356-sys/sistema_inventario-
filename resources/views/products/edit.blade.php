@extends('layouts.app')

@section('title', 'Editar producto')

@section('content')
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">Editar producto</h1>
        <p class="text-secondary mb-0">Modifique los datos de <strong>{{ $product->name }}</strong> (RF-03).</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('products.update', $product) }}" method="POST">
            @method('PUT')
            @include('products._form')
        </form>
    </div>
</div>
@endsection
