@extends('layouts.app')

@section('title', 'Editar categoría')

@section('content')
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">Editar categoría</h1>
        <p class="text-secondary mb-0">Modifique los datos de <strong>{{ $category->name }}</strong> (RF-04).</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('categories.update', $category) }}" method="POST">
            @method('PUT')
            @include('categories._form')
        </form>
    </div>
</div>
@endsection
