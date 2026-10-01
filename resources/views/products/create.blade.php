@extends('layouts.app')

@section('title', 'Registrar producto')

@section('content')
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">Registrar producto</h1>
        <p class="text-secondary mb-0">Complete los datos del nuevo producto (RF-03).</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('products.store') }}" method="POST">
            @include('products._form')
        </form>
    </div>
</div>
@endsection
