@extends('layouts.app')

@section('title', 'Nueva venta')

@section('content')
<div class="container-fluid">

    <!-- ENCABEZADO -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="titulo-artesania">
            <i class="fas fa-cash-register"></i> Nueva Venta
        </h3>
        <div>
            <a href="{{ route('cajas.index') }}" class="btn btn-outline-artesania">
                <i class="fas fa-funnel-dollar"></i> Caja
            </a>
            <a href="{{ route('venta.show') }}" class="btn btn-outline-artesania">
                <i class="fas fa-list"></i> Ventas
            </a>
        </div>
    </div>

    <!-- CARD PRINCIPAL -->
    <div class="card card-artesania">

        <div class="card-header header-artesania">
            <i class="fas fa-boxes"></i> Selección de productos
        </div>

        <div class="card-body cuerpo-artesania">
            @livewire('product-list')
        </div>

    </div>

    <!-- BOTÓN PRINCIPAL -->
    <div class="text-end mt-4">
        <button class="btn btn-artesania btn-lg" id="btnVenta">
            <i class="fas fa-check-circle"></i> Generar Venta
        </button>
    </div>

</div>

<!-- MODAL VENTA -->
<div class="modal fade" id="modalVenta" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-artesania">

            <div class="modal-header header-artesania">
                <h4 class="modal-title">
                    Total a pagar: S/ <span id="total_pagar">0.00</span>
                </h4>
                <button class="btn-close" data-dismiss="modal"></button>
            </div>

            <div class="modal-body cuerpo-artesania">

                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="label-artesania">Cliente *</label>
                        <input id="buscarCliente" class="input-artesania" placeholder="Buscar cliente">
                        <input id="id_cliente" type="hidden">
                        <small id="errorBusqueda" class="text-danger"></small>
                    </div>

                    <div class="col-md-4">
                        <label class="label-artesania">Límite crédito</label>
                        <input id="limitecredito" class="input-artesania" disabled>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="label-artesania">Teléfono</label>
                        <input id="tel_cliente" class="input-artesania" disabled>
                    </div>

                    <div class="col-md-6">
                        <label class="label-artesania">Dirección</label>
                        <input id="dir_cliente" class="input-artesania" disabled>
                    </div>
                </div>

                <div class="separador-artesania"></div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="label-artesania">Forma de pago *</label>
                        <select id="forma" class="input-artesania">
                            <option value="">Seleccionar</option>
                            @foreach ($formapagos as $formapago)
                                <option value="{{ $formapago->id }}">{{ $formapago->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="label-artesania">Método *</label>
                        <select id="metodo" class="input-artesania">
                            <option value="Contado">Contado</option>
                            <option value="Credito">Crédito</option>
                        </select>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label class="label-artesania">Pago con</label>
                        <input id="pago_con" class="input-artesania" type="number" step="0.01" oninput="calcularCambio()">
                    </div>

                    <div class="col-md-6 mt-3">
                        <label class="label-artesania">Cambio</label>
                        <input id="cambio" class="input-artesania" disabled>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-outline-artesania" data-dismiss="modal">Cancelar</button>
                <button class="btn btn-artesania" id="btnProcesar">
                    <i class="fas fa-check"></i> Completar Venta
                </button>
            </div>

        </div>
    </div>
</div>
@stop


@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" />
    <link rel="stylesheet" href="{{asset('assets/css/custom.css')}}">
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
    
    <script>
        
        var ventaUrl = "{{ route('venta.index') }}";
        var ticketUrl = "{{ route('venta.ticket', ['id' => 0]) }}";
        var limiteUrl = "{{ route('creditoventa.limitecliente', ['id' => 0]) }}";
        const btnVenta = document.querySelector('#btnVenta');
        const btnProcesar = document.querySelector('#btnProcesar');
        const total_pagar = document.querySelector('#total_pagar');
        const metodo = document.querySelector('#metodo');
        const pago_con = document.querySelector('#pago_con');
        const total = document.querySelector('#total');
        const limitecredito = document.querySelector('#limitecredito');
        document.addEventListener('DOMContentLoaded', function() {

            $("#buscarCliente").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "{{ route('venta.cliente') }}",
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
                    console.log(ui.item.id);
                    id_cliente.value = ui.item.id;
                    tel_cliente.value = ui.item.telefono;
                    dir_cliente.value = ui.item.direccion;
                    verlimitecredito(ui.item.id);
                }
            });

            btnVenta.addEventListener('click', function() {
                total_pagar.textContent = total.value;
                $('#modalVenta').modal('show');
            })

            btnProcesar.addEventListener('click', function() {
                if (id_cliente.value == '' || forma.value == '' || metodo.value == '') {
                    mostrarAlerta('TODO LOS CAMPOS CON * SON REQUERIDOS', 'warning');
                } else {
                    const montoPago = parseFloat(pago_con.value.replace(',', ''));
                    const totalPagar = parseFloat(total_pagar.textContent.replace(',', ''));

                    const esCredito = metodo.value === 'Credito';

                    if (esCredito && parseFloat(limitecredito.value) < totalPagar) {
                        mostrarAlerta("TU LIMITE DE CREDITO SUPERA AL TOTAL", 'warning');
                        return;
                    }

                    if (esCredito && montoPago >= totalPagar) {
                        mostrarAlerta("LA VENTA ES A CREDITO, INGRESE UN VALOR MENOR AL TOTAL", 'warning');
                        return;
                    }

                    Swal.fire({
                        title: "Mensaje?",
                        text: "Esta seguro de procesar la venta!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Si, procesar!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(ventaUrl, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        id_cliente: id_cliente.value,
                                        forma: forma.value,
                                        metodo: metodo.value,
                                        pago_con: montoPago,
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    mostrarAlerta(data.title, data.icon);
                                    if (data.icon == 'success') {
                                        setTimeout(() => {
                                            window.open(
                                                `/venta/`+data.ticket+`/ticket`,
                                                '_blank');
                                            window.location.reload();
                                        }, 1500);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error: ', error);
                                });
                        }
                    });
                }
            });

        })

        function verlimitecredito(id_cliente) {
            fetch(limiteUrl.replace('0', id_cliente), {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    limitecredito.value = data.toFixed(2);
                })
                .catch(error => {
                    console.error('Error: ', error);
                });
        }

        function calcularCambio() {
            var pagoCon = parseFloat(pago_con.value.replace(',', '')) || 0; // Reemplaza comas por puntos
            var totalVenta = parseFloat(total.value.replace(',', '')) || 0;

            if (!isNaN(pagoCon) && !isNaN(totalVenta)) {
                var cambio = pagoCon - totalVenta;
                document.getElementById('cambio').value = cambio.toFixed(2);
            } else {
                document.getElementById('cambio').value = '0.00';
            }
        }

        function mostrarAlerta(texto, icono) {
            Swal.fire({
                showConfirmButton: false,
                title: "Respuesta",
                text: texto,
                icon: icono,
                toast: true,
                timer: 1500,
                position: "top-end",
            });
        }
    </script>
@stop
