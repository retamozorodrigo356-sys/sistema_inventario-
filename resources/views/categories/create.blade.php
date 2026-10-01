@extends('layouts.app')

@section('title', 'Registrar categoría')

@section('content')
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">Registrar categoría</h1>
        <p class="text-secondary mb-0">Agregue una nueva categoría al sistema (RF-04).</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('categories.store') }}" method="POST">
            @include('categories._form')
        </form>
    </div>
</div>
@endsection
