@extends('layouts.app')

@section('headSection')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.css') }}">
    <!-- AdminLTE Skins -->
    <link rel="stylesheet" href="{{ asset('dist/css/skins/_all-skins.min.css') }}">
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <i class="fa fa-check-square-o" aria-hidden="true"></i> Unidades aprobadas por período
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('unidad_aprobadas.index') }}">Unidades aprobadas</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            @include('includes.messages')

            <!-- Filtros -->
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-primary">
                        <div class="box-body">
                            <form role="form" method="get" action="{{ route('unidad_aprobadas.index') }}" class="form-inline">
                                <div class="form-group">
                                    <label for="tipo">Convocatoria:&nbsp;</label>
                                    <select id="tipo" name="tipo" class="form-control" onchange="this.form.submit()">
                                        @foreach ($tipos as $key => $label)
                                            <option value="{{ $key }}" {{ $tipo == $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                &nbsp;&nbsp;
                                <div class="form-group">
                                    <label for="periodo_id">Período:&nbsp;</label>
                                    <select id="periodo_id" name="periodo_id" class="form-control" onchange="this.form.submit()">
                                        @foreach ($periodos as $periodo)
                                            <option value="{{ $periodo->id }}" {{ $periodoId == $periodo->id ? 'selected' : '' }}>{{ $periodo->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <noscript><button type="submit" class="btn btn-default">Ver</button></noscript>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @if ($periodoId)
                @if ($aprobadas->isEmpty())
                    @can('unidad-editar')
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="box box-warning">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><i class="fa fa-clone"></i> Este período no tiene unidades aprobadas — traer de otro período</h3>
                                    </div>
                                    <form role="form" method="post" action="{{ route('unidad_aprobadas.importar') }}" class="form-inline">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="tipo" value="{{ $tipo }}">
                                        <input type="hidden" name="periodo_id" value="{{ $periodoId }}">
                                        <div class="box-body">
                                            <div class="form-group">
                                                <label for="origen_tipo">Origen:&nbsp;</label>
                                                <select id="origen_tipo" name="origen_tipo" class="form-control">
                                                    @foreach ($tipos as $key => $label)
                                                        <option value="{{ $key }}" {{ $tipo == $key ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            &nbsp;&nbsp;
                                            <div class="form-group">
                                                <label for="origen_periodo_id">Período de origen:&nbsp;</label>
                                                <select id="origen_periodo_id" name="origen_periodo_id" class="form-control"></select>
                                            </div>
                                            &nbsp;&nbsp;
                                            <button type="submit" id="btnTraer" class="btn btn-warning" onclick="return confirm('¿Traer todas las unidades del período de origen a este período?');">
                                                <i class="fa fa-download"></i> Traer unidades
                                            </button>
                                            <p class="help-block">Copia todas las unidades del período elegido. Se omiten las que ya estén cargadas y las que no existan en el catálogo.</p>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endcan
                @endif
                <div class="row">
                    <!-- Agregar unidades -->
                    @can('unidad-editar')
                        <div class="col-md-5">
                            <div class="box box-success">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-plus-circle"></i> Agregar unidades</h3>
                                </div>
                                <form role="form" method="post" action="{{ route('unidad_aprobadas.agregar') }}">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="tipo" value="{{ $tipo }}">
                                    <input type="hidden" name="periodo_id" value="{{ $periodoId }}">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="unidad_ids">Unidades a aprobar</label>
                                            <select class="form-control" style="width:100%" id="unidad_ids" name="unidad_ids[]" multiple data-placeholder="Buscá por nombre o sigla...">
                                                @foreach ($disponibles as $u)
                                                    <option value="{{ $u->id }}" data-sigla="{{ $u->sigla }}">{{ $u->etiqueta }}</option>
                                                @endforeach
                                            </select>
                                            <p class="help-block">Podés seleccionar varias y buscar por nombre o sigla. Las que ya estén aprobadas se omiten.</p>
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-success">Agregar seleccionadas</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endcan

                    <!-- Aprobadas actuales -->
                    <div class="{{ auth()->user()->permissions->contains('name', 'unidad-editar') ? 'col-md-7' : 'col-md-12' }}">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Aprobadas ({{ $aprobadas->count() }})</h3>
                            </div>
                            <div class="box-body">
                                <table id="tablaAprobadas" class="table table-bordered table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>Unidad</th>
                                        <th>Sigla</th>
                                        <th style="width:90px">Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($aprobadas as $u)
                                        <tr>
                                            <td>{{ $u->nombre }}</td>
                                            <td>{{ $u->sigla }}</td>
                                            <td>
                                                @can('unidad-editar')
                                                    <form id="quitar-{{ $u->id }}" method="post" action="{{ route('unidad_aprobadas.quitar') }}" style="display:none">
                                                        {{ csrf_field() }}
                                                        <input type="hidden" name="tipo" value="{{ $tipo }}">
                                                        <input type="hidden" name="periodo_id" value="{{ $periodoId }}">
                                                        <input type="hidden" name="unidad_id" value="{{ $u->id }}">
                                                    </form>
                                                    <a href="" class="text-red" title="Quitar" onclick="
                                                        if(confirm('¿Quitar esta unidad de las aprobadas?')){
                                                            event.preventDefault();
                                                            document.getElementById('quitar-{{ $u->id }}').submit();
                                                        } else { event.preventDefault(); }">
                                                        <span class="glyphicon glyphicon-trash"></span> Quitar
                                                    </a>
                                                @else
                                                    &mdash;
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="callout callout-warning">
                    <p>No hay períodos disponibles. Cargá un período para gestionar las unidades aprobadas.</p>
                </div>
            @endif
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('footerSection')
    <!-- jQuery 3 -->
    <script src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="{{ asset('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- DataTables -->
    <script src="{{ asset('bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- SlimScroll -->
    <script src="{{ asset('bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('bower_components/fastclick/lib/fastclick.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('dist/js/demo.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        // Matcher que busca tanto por nombre (texto de la opción) como por sigla.
        function matchUnidadPorNombreOSigla(params, data) {
            if ($.trim(params.term) === '') {
                return data;
            }
            if (typeof data.text === 'undefined') {
                return null;
            }
            var term = params.term.toUpperCase();
            var texto = data.text.toUpperCase();
            var sigla = ($(data.element).data('sigla') || '').toString().toUpperCase();

            if (texto.indexOf(term) > -1 || sigla.indexOf(term) > -1) {
                return data;
            }
            return null;
        }

        // Datos para "traer de otro período".
        var conteos = @json($conteos);
        var periodos = @json($periodos);
        var destinoTipo = '{{ $tipo }}';
        var destinoPeriodo = '{{ $periodoId }}';

        function poblarOrigenPeriodos() {
            var $tipoSel = $('#origen_tipo');
            var $periodoSel = $('#origen_periodo_id');
            var $btn = $('#btnTraer');
            if (!$tipoSel.length || !$periodoSel.length) {
                return;
            }
            var ot = $tipoSel.val();
            var mapa = conteos[ot] || {};
            $periodoSel.empty();

            var opciones = [];
            periodos.forEach(function (p) {
                var n = mapa[p.id] || 0;
                if (n > 0) {
                    // Excluir el mismo destino (misma tabla y mismo período).
                    if (ot === destinoTipo && String(p.id) === String(destinoPeriodo)) {
                        return;
                    }
                    opciones.push({ id: p.id, nombre: p.nombre, n: n });
                }
            });

            if (opciones.length === 0) {
                $periodoSel.append('<option value="">(sin períodos con datos)</option>');
                $btn.prop('disabled', true);
            } else {
                opciones.forEach(function (o) {
                    $periodoSel.append('<option value="' + o.id + '">' + o.nombre + ' (' + o.n + ' unidades)</option>');
                });
                $btn.prop('disabled', false);
            }
        }

        $(function () {
            $('#unidad_ids').select2({
                placeholder: $('#unidad_ids').data('placeholder'),
                width: '100%',
                matcher: matchUnidadPorNombreOSigla
            });

            $('#origen_tipo').on('change', poblarOrigenPeriodos);
            poblarOrigenPeriodos();

            $('#tablaAprobadas').DataTable({
                "autoWidth": false,
                responsive: true,
                "language": {
                    "url": "{{ asset('bower_components/datatables.net/lang/es-AR.json') }}"
                }
            });
        });
    </script>
@endsection
