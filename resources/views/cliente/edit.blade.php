@extends('layouts.app')

@section('title', 'Editar | ' . $cliente->nombre)

@section('content')
    <div class="">
        <div class="col-md-12">

            @includeif('partials.errors')

            <div class="card card-default">
                <div class="card-header">
                    <span class="card-title">{{ __('Update') }} Cliente</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('clientes.update', $cliente->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{ Form::model($cliente, ['route' => ['clientes.update', $cliente->id], 'method' => 'PUT']) }}
                            @include('cliente.form')
                        {{ Form::close() }}
                    </form>

                </div>
            </div>
        </div>
    </div>
@stop
