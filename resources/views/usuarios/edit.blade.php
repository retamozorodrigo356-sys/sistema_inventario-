@extends('layouts.app')

@section('title', 'Editar usuario')

@section('content')
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">Editar usuario</h1>
        <p class="text-secondary mb-0">Modifique los datos de <strong>{{ $user->name }}</strong> (RF-02).</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('usuarios.update', $user) }}" method="POST">
            @method('PUT')
            @include('usuarios._form')
        </form>
    </div>
</div>
@endsection
