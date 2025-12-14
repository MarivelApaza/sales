@extends('layouts.app')

@section('title', 'Nueva compra')

@section('content')
<div class="container-fluid">

    <!-- ENCABEZADO -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="titulo-artesania">
            <i class="fas fa-store"></i> Registro de Compra Artesanal
        </h3>
        <div>
            <a href="{{ route('cajas.index') }}" class="btn btn-outline-artesania">
                <i class="fas fa-cash-register"></i> Caja
            </a>
            <a href="{{ route('compra.show') }}" class="btn btn-outline-artesania">
                <i class="fas fa-list"></i> Compras
            </a>
        </div>
    </div>

    <!-- TARJETA -->
    <div class="card card-artesania">

        <div class="card-header header-artesania">
            <i class="fas fa-truck"></i> Información del Proveedor
        </div>

        <div class="card-body cuerpo-artesania">

            <!-- PROVEEDOR -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="label-artesania">Proveedor</label>
                    <input id="buscarProveedor" class="input-artesania" placeholder="Buscar proveedor">
                    <input id="id_proveedor" type="hidden">
                    <small id="errorBusqueda" class="text-danger"></small>
                </div>

                <div class="col-md-4">
                    <label class="label-artesania">Teléfono</label>
                    <input id="tel_proveedor" class="input-artesania" disabled>
                </div>

                <div class="col-md-4">
                    <label class="label-artesania">Dirección</label>
                    <input id="dir_proveedor" class="input-artesania" disabled>
                </div>
            </div>

            <div class="separador-artesania"></div>

            <!-- PRODUCTOS -->
            <h5 class="subtitulo-artesania">
                <i class="fas fa-box"></i> Detalle de productos
            </h5>

            @livewire('product-compra')

        </div>
    </div>

    <!-- BOTÓN PRINCIPAL -->
    <div class="text-end mt-4">
        <button id="btnCompra" class="btn btn-artesania btn-lg">
            <i class="fas fa-check"></i> Registrar Compra
        </button>
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
        var compraUrl = "{{ route('compra.index') }}";
        var ticketUrl = "{{ route('compra.ticket', ['id' => 0]) }}";

        const btnCompra = document.querySelector('#btnCompra');

        document.addEventListener('DOMContentLoaded', function() {
            $("#buscarProveedor").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "{{ route('compra.proveedor') }}",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            if (data.length === 0) {
                                errorBusqueda.textContent =
                                    "No se encontraron resultados.";
                            } else {
                                errorBusqueda.textContent = '';
                            }
                            response(data);
                        }
                    });
                },
                minLength: 2, // Número mínimo de caracteres para mostrar sugerencias
                select: function(event, ui) {
                    id_proveedor.value = ui.item.id;
                    tel_proveedor.value = ui.item.telefono,
                        dir_proveedor.value = ui.item.direccion
                }
            });
            btnCompra.addEventListener('click', function() {
                if (id_proveedor.value == '') {
                    Swal.fire({
                        title: "Respuesta",
                        text: 'El proveedor es requerido',
                        icon: 'warning'
                    });
                } else {
                    Swal.fire({
                        title: "Mensaje?",
                        text: "Esta seguro de procesar la compra!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Si, procesar!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(compraUrl, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        id_proveedor: id_proveedor.value
                                    })
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(
                                            `La solicitud falló con estado: ${response.status}`
                                        );
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    Swal.fire({
                                        title: "Respuesta",
                                        text: data.title,
                                        icon: data.icon
                                    });

                                    if (data.icon === 'success') {
                                        setTimeout(() => {
                                            window.open(
                                                `/compra/`+data.ticket+`/ticket`,
                                                '_blank');
                                            window.location.href =
                                                compraUrl; // Puedes cambiar esto según tus necesidades
                                        }, 1500);
                                    } else {
                                        // Manejar otros casos si es necesario
                                    }
                                })
                                .catch(error => {
                                    console.error('Error en la solicitud:', error);
                                    Swal.fire({
                                        title: "Error",
                                        text: "Hubo un problema al procesar la solicitud.",
                                        icon: "error"
                                    });
                                });

                        }
                    });
                }

            })
        })
    </script>
@stop
