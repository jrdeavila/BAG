 @props(['breadcrumbs' => []])

 @extends('adminlte::page')

 @section('content_header')
     <div class="row align-items-center w-100 px-3">
         @yield('header')
         <div class="mx-auto">
         </div>
         <x-breadcrumbs />
     </div>
 @endsection
