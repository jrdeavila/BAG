@extends('adminlte::page')


@section('title', 'Página no encontrada')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>403 Accion no permitida</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">403 Acción no permitida</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="error-page">
        <h2 class="headline text-primary"> 403</h2>

        <div class="error-content">
            <h3><i class="fas fa-exclamation-triangle text-primary"></i> Ups! Acción no permitida.</h3>

            <p>
                No tienes permiso para acceder a esta página.
                Mientras tanto, puedes <a href="{{ url('/') }}">volver al panel</a> o intentar usar el formulario de
            </p>

            <form class="search-form">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search">

                    <div class="input-group-append">
                        <button type="submit" name="submit" class="btn btn-primary"><i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
