@extends('layouts.app')

@section('title', 'Registrar usuario')

@section('content')
<div class="page-header">
    <div>
        <h1 class="h4 mb-1">Registrar usuario</h1>
        <p class="text-secondary mb-0">Cree un usuario y asigne su rol (RF-02).</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('usuarios.store') }}" method="POST">
            @include('usuarios._form')
        </form>
    </div>
</div>
@endsection
