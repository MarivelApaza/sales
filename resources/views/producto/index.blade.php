@extends('layouts.app')

@section('title', 'Productos')

@section('content')
<div class="artesania-theme"> 
<div class="row justify-content-center">
    <div class="col-lg-12">

        <!-- CABECERA -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-choco fw-bold mb-0">
                <i class="fas fa-boxes"></i> Productos
            </h4>

            <div class="btn-group">
                @can('productos.create')
                    <a href="{{ route('productos.create') }}" class="btn btn-choco btn-sm">
                        <i class="fas fa-plus"></i> Nuevo
                    </a>
                @endcan

                @can('productos.reportes')
                    <a href="{{ route('productos.pdf') }}" target="_blank" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                    <a href="{{ route('productos.excel') }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-file-excel"></i>
                    </a>
                    <a href="{{ route('productos.barcode') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-barcode"></i>
                    </a>
                @endcan
            </div>
        </div>

        <!-- CARD -->
        <div class="card product-card shadow-sm">
            <div class="card-body bg-crema">

                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ $message }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle product-table nowrap w-100 dataTable" id="tblProducts">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>P. Compra</th>
                                <th>P. Venta</th>
                                <th>Stock</th>
                                <th>Categoría</th>
                                <th>Imagen</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<form id="deleteForm" action="#" method="post">
    @csrf
    @method('DELETE')
</form>
@stop


@section('css')
    <link href="DataTables/datatables.min.css" rel="stylesheet">
@endsection

@section('js')
    <script src="DataTables/datatables.min.js"></script>
    <script>
        var editUrl = "{{ route('productos.edit', ['producto' => 0]) }}";
        var deleteUrl = "{{ route('productos.destroy', ['producto' => 0]) }}";
        document.addEventListener("DOMContentLoaded", function() {
            new DataTable('#tblProducts', {
                responsive: true,
                fixedHeader: true,
                dom: 'Pfrtip', // Agrega los elementos necesarios para SearchPane
                searchPanes: {
                    columns: [6]
                },
                ajax: {
                    url: '{{ route('products.list') }}',
                    dataSrc: 'data'
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'codigo'
                    },
                    {
                        data: 'producto'
                    },
                    {
                        data: 'precio_compra'
                    },
                    {
                        data: 'precio_venta'
                    },
                    {
                        data: 'stock'
                    },
                    {
                        data: 'categoria'
                    },
                    {
                        data: 'foto',
                        render: function(data, type, row) {
                            return data ? '<img src="storage/' + data +
                                '" alt="Imagen del Producto" style="max-width: 100px; max-height: 100px;">' :
                                'Sin imagen';
                        }
                    },
                    {
                        // Agregar columna para acciones
                        data: null,
                        render: function(data, type, row) {
                            // Agregar botones de editar y eliminar
                            return `<a class="btn btn-sm btn-primary" href="/productos/${row.id}/edit"><i class="fas fa-edit"></i></a>` +
                                '<button class="btn btn-sm btn-danger" onclick="deleteProduct(' +
                                row.id + ')"><i class="fas fa-trash"></i></button>';
                        }
                    }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.3/i18n/es-ES.json',
                },
                order: [
                    [0, 'desc']
                ]
            });
        });

        // Función para eliminar un producto
        function deleteProduct(productId) {
            Swal.fire({
                title: "Eliminar",
                text: "¿Estás seguro de que quieres eliminar este producto?",
                icon: "info",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, eliminar!"
            }).then((result) => {
                if (result.isConfirmed) {
                    var form = document.querySelector('#deleteForm');
                    form.action = deleteUrl.replace('0', productId);
                    // Enviar el formulario
                    form.submit();
                }
            });
        }
    </script>
@stop
