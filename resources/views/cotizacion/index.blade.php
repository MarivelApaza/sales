@extends('layouts.app')

@section('title', 'Nueva cotización')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-11">

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h4 class="text-choco fw-bold mb-0">
                <i class="fas fa-file-invoice"></i> Nueva Cotización
            </h4>

            <a href="{{ route('cotizacion.show') }}" class="btn btn-outline-choco btn-sm">
                <i class="fas fa-list mr-1"></i> Listar cotizaciones
            </a>
        </div>

        <div class="card cotizacion-card shadow-sm">
            <div class="card-body bg-crema">

                <!-- Cliente -->
                <div class="row mb-3">
                    <div class="col-md-4 mb-2">
                        <label class="label-choco">Cliente</label>
                        <div class="input-group">
                            <span class="input-group-text icon-choco">
                                <i class="fas fa-search"></i>
                            </span>
                            <input id="buscarCliente" class="form-control input-crema" type="text"
                                   placeholder="Buscar cliente">
                            <input id="id_cliente" type="hidden">
                        </div>
                        <small id="errorBusqueda" class="text-danger"></small>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label class="label-choco">Teléfono</label>
                        <div class="input-group">
                            <span class="input-group-text icon-choco">
                                <i class="fas fa-phone"></i>
                            </span>
                            <input id="tel_cliente" class="form-control input-crema" disabled>
                        </div>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label class="label-choco">Dirección</label>
                        <div class="input-group">
                            <span class="input-group-text icon-choco">
                                <i class="fas fa-home"></i>
                            </span>
                            <input id="dir_cliente" class="form-control input-crema" disabled>
                        </div>
                    </div>
                </div>

                <hr class="divider-choco">

                <!-- Productos -->
                @livewire('product-cotizacion')

                <!-- Botón -->
                <div class="text-end mt-4">
                    <button class="btn btn-choco btn-lg px-4" id="btnCotizacion" type="button">
                        <i class="fas fa-file-invoice-dollar mr-1"></i> Generar cotización
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
@stop


@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
    <script>
        var cotizacionUrl = "{{ route('cotizacion.index') }}";
        var ticketUrl = "{{ route('cotizacion.ticket', ['id' => 0]) }}";
        const btnCotizacion = document.querySelector('#btnCotizacion');
        document.addEventListener('DOMContentLoaded', function() {
            $("#buscarCliente").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "{{ route('cotizacion.cliente') }}",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            if (data.length === 0) {
                                errorBusqueda.textContent = "No se encontraron resultados.";
                            } else {
                                errorBusqueda.textContent = '';
                            }
                            response(data);
                        }
                    });
                },
                minLength: 2, // Número mínimo de caracteres para mostrar sugerencias
                select: function(event, ui) {
                    id_cliente.value = ui.item.id;
                    tel_cliente.value = ui.item.telefono,
                        dir_cliente.value = ui.item.direccion
                }
            });
            btnCotizacion.addEventListener('click', function() {
                if (id_cliente.value == '') {
                    Swal.fire({
                        title: "Respuesta",
                        text: 'El cliente es requerido',
                        icon: 'warning'
                    });
                } else {
                    Swal.fire({
                        title: "Mensaje?",
                        text: "Esta seguro de procesar la cotizacion!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Si, procesar!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(cotizacionUrl, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        id_cliente: id_cliente.value
                                    })
                                })
                                .then(response => {
                                    return response.json();
                                })
                                .then(data => {
                                    Swal.fire({
                                        title: "Respuesta",
                                        text: data.title,
                                        icon: data.icon
                                    });
                                    if (data.icon == 'success') {
                                        setTimeout(() => {
                                            window.open(
                                                `/cotizacion/`+data.ticket+`/ticket`,
                                                '_blank');
                                            window.location.reload();
                                        }, 1500);
                                    } else {

                                    }
                                })
                                .catch(error => {
                                    // Manejar errores
                                    console.error('Error: ', error);
                                });
                        }
                    });
                }

            })
        })
    </script>
@stop
