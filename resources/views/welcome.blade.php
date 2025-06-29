@extends('layouts.guest')

@section('content')
    <div class="d-flex flex-column justify-content-center">

        <h5 class="text-center">
            Bienvenido al sistema de autorización digital de la Camara de comercio de Valledupar
        </h5>
        <div class="my-4"></div>
        @auth
            <form class="w-100" action="{{ route('home') }}" method="GET">
                @csrf
                <x-adminlte-button class="btn-flat w-100" type="submit" label="Ir a la plataforma" icon="fas fa-home" />
            </form>
        @else
            <form class="w-100" action="{{ route('login') }}" method="GET">
                @csrf
                <x-adminlte-button class="btn-flat w-100" type="submit" label="Iniciar Sesión" icon="fas fa-sign-in-alt" />
            </form>
        @endauth
    </div>
@endsection
