@extends('layouts.app')
@section('headSection')

    <!-- AdminLTE Skins. Choose a skin from the css/skins
           folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('dist/css/skins/_all-skins.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
<style>
    /* Quitar viñetas de la lista desplegable */
    .ui-autocomplete {
        list-style: none;
        padding: 0;
        margin: 0;
        max-height: 200px; /* Ajustar la altura máxima de la lista */
        overflow-y: auto;  /* Agregar desplazamiento vertical si es necesario */
        z-index: 1000;     /* Asegurar que la lista se muestre encima de otros elementos */
    }

    /* Estilo de los elementos de la lista */
    .ui-menu-item {
        padding: 10px;
        border-bottom: 1px solid #ccc;
        background-color: #fff;
    }

    /* Estilo de los elementos cuando se hace hover o se seleccionan */
    .ui-menu-item:hover, .ui-menu-item.ui-state-focus {
        background-color: #f0f0f0;
    }
    table tr td {

        border-right: 1px solid #ccc;
        padding: 4px;
        text-align: center;
        vertical-align: top;
    }

    .fValidator-a {
        color: #708090;
        cursor: pointer;
        font-size: 11px;
        font-weight: bold;
    }
</style>

@endsection


@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <i class="fa fa-clipboard-check" aria-hidden="true"></i>Evaluación Viajes/Estadías
                <small>Evaluar solicitud</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('viajes.index') }}">Viajes/Estadías</a></li>

            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">



                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" id="formEvaluar" action="{{ route('viaje_evaluacions.saveEvaluar',$evaluacion->id) }}" method="post" enctype="multipart/form-data" onsubmit="return prepararEnvio();">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            {{Form::hidden('viaje_evaluacion_planilla_id', $planilla->id)}}
                            <div class="box-body">
                                @include('includes.messages')
                                <a name="ancla" id="ancla"></a>
<fieldset>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Solicitante:</label>
                                                    <span>{{ $viaje->investigador->persona->apellido }} {{ $viaje->investigador->persona->nombre }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>U. Académica:</label>
                                                    <span>{{ $facultad->nombre }}</span>
                                                </div>
                                            </div>

                                        </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Motivo de la Solicitud:</label>
                <span>{{ $planilla->motivo }} {{ $planilla->tipo }}</span>
            </div>
        </div>


    </div>

                                    <table style="width:100%">
                                        <tbody>
                                        <tr style="background-color: #333;color:#fff">
                                            <td style="width:80px">P. Max/ITEM</td>
                                            <td>DETALLE Y PUNTAJE</td>
                                            <td style="width:90px">P. OTORGADO</td>
                                        </tr>
                                        </tbody></table>

    <table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666 ;margin-bottom:5px">
        <tbody>

        <tr style="border-style: solid; border-width: 1px; border-color: #666"><td style="background-color: #eee;color:#333; width:80px">CATEGORIA<br><strong>Max. {{$maxCategoria}}pt.</strong></td><td style="background-color: #fff; border-width: 0px; border-color: #fff">
                @foreach($categoriaMaximos as $categoriaMaximo)
                    @php
                        $checked = ($categoria_id== $categoriaMaximo->categoria_id) ? ' CHECKED ' : '';
                    @endphp


                    <input name="viaje_planilla_categoria_max_id" id="viaje_planilla_categoria_max_id{{ $loop->index}}" type="radio" value="{{$categoriaMaximo->id}}-{{$categoriaMaximo->maximo}}" onclick="sumar_total();"{{$checked}}' DISABLED>
                    <input name="categoria_maximo{{ $loop->index}}" id="categoria_maximo{{ $loop->index}}" type="hidden" value="{{$categoriaMaximo->maximo}}">{{$categoriaMaximo->categoria_nombre}} ({{$categoriaMaximo->maximo}}pt.)</td><td style="background-color: #fff; border-width: 0px; border-color: #fff">

            @endforeach

            <td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;"><div align="right"><input name="categoria_puntaje" id="categoria_puntaje" type="hidden" value="0"><span id="spanCategoria">0</span></div></td></tr></tbody></table>

    @php
        $cargo_id=0;
        //dd($cargoMaximos);
        switch ($viaje->cargo_id) {
            case '1':
                $cargo_id = 1;
            break;
            case '2':
                $cargo_id = 5;
            break;
            case '3':
                $cargo_id = 3;
            break;
            case '4':
                $cargo_id = 7;
            break;
            case '5':
                $cargo_id = 9;
            break;
            case '7':
                $cargo_id = 2;
            break;
            case '8':
                $cargo_id = 6;
            break;
            case '9':
                $cargo_id = 4;
            break;
            case '10':
                $cargo_id = 8;
            break;
            case '11':
                $cargo_id = 10;
            break;
        }

    @endphp
    <table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666;margin-bottom:5px">
        <tbody>
            <tr style="border-style: solid; border-width: 1px; border-color: #666">
                <td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;">CARGO DOCENTE ACTUAL EN LA UNLP<br><strong>Max. {{$maxCargo}}pt.</strong> </td>
                <td>
                    <table style="width:100%">
                        <tbody>
                            <tr>
                                @foreach($cargoMaximos->chunk(2) as $chunk) <!-- Divide los cargos en grupos de 2 -->
                            <tr>
                                @foreach($chunk as $index => $cargoMaximo) <!-- Recorre cada "chunk" de 2 elementos -->
                                @php
                                    $checked = ($cargo_id== $cargoMaximo->cargo_id) ? ' CHECKED ' : '';
                                @endphp

                                <td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;">
                                    <input name="cargomaximo"
                                           id="cargomaximo{{ $loop->index + $index }}"
                                           type="radio"
                                           value="{{ $cargoMaximo->id }}-{{ $cargoMaximo->maximo }}"
                                           onclick="sumar_total();"
                                           {{ $checked }}
                                           DISABLED />

                                    <input name="maxcargo{{ $loop->index + $index }}"
                                           id="maxcargo{{ $loop->index + $index }}"
                                           type="hidden"
                                           value="{{ $cargoMaximo->maximo }}" />

                                    {{ $cargoMaximo->cargo_nombre }} ({{ $cargoMaximo->maximo }}pt.)
                                </td>
                                @endforeach
                            </tr>
                            @endforeach

                           </tbody></table></td><td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;"><div align="right"><span id="spanCargo">2</span></div></td></tr>
        </tbody></table>
    @php
        $primeritems = $itemMaximos->first();
        $periodoActual =intval($viaje->periodo->nombre);
        //dd($primeritems);
    @endphp

    <table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666;margin-bottom:5px">
        <tbody>
        <input type="hidden" name="iterador1" id="iterador1" value="{{$planilla->iterador1}}">
        <tr style="border-style: solid; border-width: 1px; border-color: #666">
            @php
                $submax = 0;
                $submax2 = 0;
                $sub = 0;
                $j = 0;
                $max = 0;
                $maxGrupoSiguiente = 0;//como voy recorrrelo todo aprovecho a calcular el maximo de los RRHH
            @endphp
            @foreach($itemMaximos as $index => $itemMaximo)
                {{-- Primera iteración: Hasta iterador1 --}}
                @if($index < $planilla->iterador1)


                <!-- Verificar si se cambia de grupo para mostrar el subtotal -->
        @if ($submax != $itemMaximo->evaluacion_grupo_id)
            @php
                //echo $itemMaximos[$loop->index]->grupo_maximo;
            @endphp

            @if ($loop->index != 0 && $itemMaximos[$loop->index - 1]->grupo_maximo != 0)
                <tr style="background-color: #eee; color: #333;">
                    <td></td>
                    <td colspan="2">
                        <div align="right">
                            <strong>Subtotal (max. {{ $itemMaximos[$loop->index - 1]->grupo_maximo }})</strong>
                        </div>
                        <input type="hidden"
                               name="maxgrupoitem{{ $itemMaximos[$loop->index - 1]->evaluacion_grupo_id }}"
                               id="maxgrupoitem{{ $itemMaximos[$loop->index - 1]->evaluacion_grupo_id }}"
                               value="{{ $itemMaximos[$loop->index - 1]->grupo_maximo }}" />
                        <strong>
                            <div id="divgrupoitems{{ $itemMaximos[$loop->index - 1]->evaluacion_grupo_id }}" class="fValidator-a"></div>
                        </strong>
                    </td>
                    <td style="text-align:right"><strong><span id="spangrupoitem{{ $itemMaximos[$loop->index - 1]->evaluacion_grupo_id }}" ></span></strong><div id="divgrupoitem{{ $itemMaximos[$loop->index - 1]->evaluacion_grupo_id }}" class="fValidator-a"></div></td>
                </tr>
            @endif

            @php
                $submax = $itemMaximo->evaluacion_grupo_id;
            @endphp
        @endif


        @php
            $puntajeitem = $evaluacion->puntaje_items->where('viaje_evaluacion_planilla_item_max_id', $itemMaximo->id)->first();
            $puntaje = (($puntajeitem)&&($puntajeitem->puntaje))?(int)$puntajeitem->puntaje:'';
            $cantidad = (($puntajeitem)&&($puntajeitem->cantidad))?$puntajeitem->cantidad:'';
            //$tope = (($itemMaximo->tope==0)||($itemMaximo->tope==$itemMaximo->minimo))?'':'<strong>Max. '.$itemMaximo->tope.'pt.</strong>';
            $step = '1';

            $input = ($itemMaximo->minimo!=$itemMaximo->maximo)?'<td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;"><input type="number" step="1" min="0" size="5" name="cantitem'.$loop->index.'" id="cantitem'.$loop->index.'" value="'.$cantidad.'" onblur="validarCampos(' . $loop->index . ', \'item\'); sumar_total();"></td><td style="background-color: #eee;color:#333;" align="right"><input type="number" step="'.$step.'" min="0" size="5" name="puntajeitem'.$loop->index.'" id="puntajeitem'.$loop->index.'" value="'.$puntaje.'" onblur="validarCampos(' . $loop->index . ', \'item\'); sumar_total();"><div id="divpuntajeitem'.$loop->index.'" class="fValidator-a"></div><input name="valoritem'.$loop->index.'" id="valoritem'.$loop->index.'" type="hidden" value=""/></td>':'<td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;"><input type="number" step="'.$step.'" min="0" size="5" name="puntajeitem'.$loop->index.'" id="puntajeitem'.$loop->index.'" value="'.$puntaje.'" onblur="sumar_total();"></td><td style="background-color: #eee;color:#333; width:80px; text-align: right; vertical-align:middle;"><span id="spanpuntajeitem'.$loop->index.'" ></span><div id="divpuntajeitem'.$loop->index.'" class="fValidator-a"></div><input name="valoritem'.$loop->index.'" id="valoritem'.$loop->index.'" type="hidden" value=""/></td>';
            $c_u=' c/u';
		 	/*$hasta = (($itemMaximo->maximo!=0)&&($itemMaximo->minimo==$itemMaximo->maximo))?((($itemMaximo->minimo==$itemMaximo->tope)&&($itemMaximo->minimo==$itemMaximo->maximo))?$itemMaximo->maximo. ' pt.':$itemMaximo->maximo. $c_u):(($itemMaximo->minimo!=$itemMaximo->maximo)?'Hasta '.$itemMaximo->maximo. ' c/u':'Hasta '.$itemMaximo->tope);*/
		 	$hasta =$itemMaximo->maximo. $c_u;
            $descripcion = str_replace('#puntaje#', '<B>'.$itemMaximo->maximo.' puntos</B>',$itemMaximo->item_nombre);
        @endphp
@if($primeritems->id == $itemMaximo->id)
    <td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;" rowspan="{{($planilla->iterador1 + 4)}}">PROD. ULTIMOS 5 AÑOS {{($planilla->motivo!='C')?'':' DEL SOLICITANTE'}}<br><strong>Max. {{$itemMaximo->padre_maximo}}pt.</strong></td>
@endif



        <td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;width: 60%;">{!! $descripcion !!}</td>
        <td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;width: 20%;">
            <input type="hidden"  name="id_item{{$loop->index}}" id="id_item{{$loop->index}}" value="{{$itemMaximo->id}}">
            <input type="hidden"  name="maxitem{{$loop->index}}" id="maxitem{{$loop->index}}" value="{{$itemMaximo->maximo}}">

            <input type="hidden"  name="minitem{{$loop->index}}" id="minitem{{$loop->index}}" value="{{$itemMaximo->minimo}}">
            <input type="hidden"  name="item_evaluacion_grupo_id{{$loop->index}}" id="item_evaluacion_grupo_id{{$loop->index}}" value="{{$itemMaximo->evaluacion_grupo_id}}">{!! $hasta !!}
        </td>{!! $input !!}
        </tr>




        @else
            @php
                if ($submax!=$itemMaximo->evaluacion_grupo_id ){
                    $maxGrupoSiguiente +=$itemMaximo->grupo_maximo;
                }
                $submax = $itemMaximo->evaluacion_grupo_id;
            @endphp

        @endif
        @endforeach

        @php
            $ultimoitemMaximo=$itemMaximos->take($planilla->iterador1)->last();
            //dd($ultimoitemMaximo);

        @endphp
        @if($ultimoitemMaximo->grupo_maximo != 0)
            <tr style="background-color: #eee;color:#333;"">
            <td></td>
            <td colspan="2">
                <div align="right"><strong>Subtotal (max. {{$ultimoitemMaximo->grupo_maximo}})</strong></div>
                <input type="hidden"  name="maxgrupoitem{{$ultimoitemMaximo->evaluacion_grupo_id}}" id="maxgrupoitem{{$ultimoitemMaximo->evaluacion_grupo_id}}" value="{{$ultimoitemMaximo->grupo_maximo}}">
            </td>
            <td style="text-align:right"><strong><span id="spangrupoitem{{$ultimoitemMaximo->evaluacion_grupo_id}}" ></span></strong><div id="divgrupoitem{{$ultimoitemMaximo->evaluacion_grupo_id}}" class="fValidator-a"></div></td>
            </tr>
        @endif
        @if($primeritems->padre_id)
            <tr style="background-color: #eee;color:#333;"">
            <td colspan="3">
                <div align="right">
                    <strong>Subtotal PROD. ULTIMOS 5 AÑOS (max. {{$primeritems->padre_maximo}})</strong>
                </div>
                <input type="hidden"  name="maxgrupoitemG1" id="maxgrupoitemG1" value="{{$primeritems->padre_maximo}}"></td><td style="text-align:right">
                <strong><span id="spangrupoitemG1" ></span></strong><div id="divgrupoitemG1" class="fValidator-a"></div>
            </td>
            </tr>
        @endif




        </tbody></table>
    @php

        $primeritems = $itemMaximos->take($planilla->iterador1+1)->last();

    @endphp
    <table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666;margin-bottom:5px">
        <tbody>
        <input type="hidden" name="iterador2" id="iterador2" value="{{$planilla->iterador2}}">
        <tr style="border-style: solid; border-width: 1px; border-color: #666">
            @php
                $submax = 0;
                $sub = 0;
                $j = 0;
                $max = 0;

            @endphp
            @foreach($itemMaximos as $index => $itemMaximo)
                {{-- Primera iteración: Hasta iterador1 --}}
                @if($index >= $planilla->iterador1 && $index < $planilla->iterador2)


                    <!-- Verificar si se cambia de grupo para mostrar el subtotal -->
        @if ($submax != $itemMaximo->evaluacion_grupo_id)
            @php
                //echo $itemMaximos[$loop->index]->grupo_maximo;

            @endphp

            @if ($loop->index != 0 && $loop->index != $planilla->iterador1 && $itemMaximos[$loop->index - 1]->grupo_maximo != 0)

                <tr style="background-color: #eee; color: #333;">
                    <td></td>
                    <td colspan="2">
                        <div align="right">
                            <strong>Subtotal (max. {{ $itemMaximos[$loop->index - 1]->grupo_maximo }})</strong>
                        </div>
                        <input type="hidden"
                               name="maxgrupoitem{{ $itemMaximos[$loop->index - 1]->evaluacion_grupo_id }}"
                               id="maxgrupoitem{{ $itemMaximos[$loop->index - 1]->evaluacion_grupo_id }}"
                               value="{{ $itemMaximos[$loop->index - 1]->grupo_maximo }}" />
                        <strong>
                            <div id="divgrupoitems{{ $itemMaximos[$loop->index - 1]->evaluacion_grupo_id }}" class="fValidator-a"></div>
                        </strong>
                    </td>
                    <td style="text-align:right"><strong><span id="spangrupoitem{{ $itemMaximos[$loop->index - 1]->evaluacion_grupo_id }}" ></span></strong><div id="divgrupoitem{{ $itemMaximos[$loop->index - 1]->evaluacion_grupo_id }}" class="fValidator-a"></div></td>
                </tr>
            @endif

            @php
                $submax = $itemMaximo->evaluacion_grupo_id;
            @endphp
        @endif


        @php
            $puntajeitem = $evaluacion->puntaje_items->where('viaje_evaluacion_planilla_item_max_id', $itemMaximo->id)->first();
            $puntaje = (($puntajeitem)&&($puntajeitem->puntaje))?(int)$puntajeitem->puntaje:'';
            $cantidad = (($puntajeitem)&&($puntajeitem->cantidad))?$puntajeitem->cantidad:'';
            //$tope = (($itemMaximo->tope==0)||($itemMaximo->tope==$itemMaximo->minimo))?'':'<strong>Max. '.$itemMaximo->tope.'pt.</strong>';
            $step = '1';

            $input = ($itemMaximo->minimo!=$itemMaximo->maximo)?'<td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;"><input type="number" step="1" min="0" size="5" name="cantitem'.$loop->index.'" id="cantitem'.$loop->index.'" value="'.$cantidad.'" onblur="validarCampos(' . $loop->index . ', \'item\'); sumar_total();"></td><td style="background-color: #eee;color:#333;" align="right"><input type="number" step="'.$step.'" min="0" size="5" name="puntajeitem'.$loop->index.'" id="puntajeitem'.$loop->index.'" value="'.$puntaje.'" onblur="validarCampos(' . $loop->index . ', \'item\'); sumar_total();"><div id="divpuntajeitem'.$loop->index.'" class="fValidator-a"></div><input name="valoritem'.$loop->index.'" id="valoritem'.$loop->index.'" type="hidden" value=""/></td>':'<td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;"><input type="number" step="'.$step.'" min="0" size="5" name="puntajeitem'.$loop->index.'" id="puntajeitem'.$loop->index.'" value="'.$puntaje.'" onblur="sumar_total();"></td><td style="background-color: #eee;color:#333; width:80px; text-align: right; vertical-align:middle;"><span id="spanpuntajeitem'.$loop->index.'" ></span><div id="divpuntajeitem'.$loop->index.'" class="fValidator-a"></div><input name="valoritem'.$loop->index.'" id="valoritem'.$loop->index.'" type="hidden" value=""/></td>';
            $c_u=' c/u';
		 	/*$hasta = (($itemMaximo->maximo!=0)&&($itemMaximo->minimo==$itemMaximo->maximo))?((($itemMaximo->minimo==$itemMaximo->tope)&&($itemMaximo->minimo==$itemMaximo->maximo))?$itemMaximo->maximo. ' pt.':$itemMaximo->maximo. $c_u):(($itemMaximo->minimo!=$itemMaximo->maximo)?'Hasta '.$itemMaximo->maximo. ' c/u':'Hasta '.$itemMaximo->tope);*/
		 	$hasta =$itemMaximo->maximo. $c_u;
            $descripcion = str_replace('#puntaje#', '<B>'.$itemMaximo->maximo.' puntos</B>',$itemMaximo->item_nombre);
        @endphp

        @if($primeritems->id == $itemMaximo->id)
            <td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;" rowspan="{{(($planilla->iterador2-$planilla->iterador1) + 3)}}">FORMACION RECURSOS HUMANOS<br><strong>Max. {{$maxGrupoSiguiente}}pt.</strong></td>
        @endif



        <td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;width: 60%;">{!! $descripcion !!}</td>
        <td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;width: 20%;">
            <input type="hidden"  name="id_item{{$loop->index}}" id="id_item{{$loop->index}}" value="{{$itemMaximo->id}}">
            <input type="hidden"  name="maxitem{{$loop->index}}" id="maxitem{{$loop->index}}" value="{{$itemMaximo->maximo}}">

            <input type="hidden"  name="minitem{{$loop->index}}" id="minitem{{$loop->index}}" value="{{$itemMaximo->minimo}}">
            <input type="hidden"  name="item_evaluacion_grupo_id{{$loop->index}}" id="item_evaluacion_grupo_id{{$loop->index}}" value="{{$itemMaximo->evaluacion_grupo_id}}">{!! $hasta !!}
        </td>{!! $input !!}
        </tr>





        @endif
        @endforeach

        @php

            $ultimoitemMaximo=$itemMaximos->take($planilla->iterador2)->last();
            //dd($ultimoitemMaximo);

        @endphp
        @if($ultimoitemMaximo->grupo_maximo != 0)
            <tr style="background-color: #eee;color:#333;"">
            <td></td>
            <td colspan="2">
                <div align="right"><strong>Subtotal (max. {{$ultimoitemMaximo->grupo_maximo}})</strong></div>
                <input type="hidden"  name="maxgrupoitem{{$ultimoitemMaximo->evaluacion_grupo_id}}" id="maxgrupoitem{{$ultimoitemMaximo->evaluacion_grupo_id}}" value="{{$ultimoitemMaximo->grupo_maximo}}">
            </td>
            <td style="text-align:right"><strong><span id="spangrupoitem{{$ultimoitemMaximo->evaluacion_grupo_id}}" ></span></strong><div id="divgrupoitem{{$ultimoitemMaximo->evaluacion_grupo_id}}" class="fValidator-a"></div></td>
            </tr>
        @endif
        @if($maxGrupoSiguiente)
            <tr style="background-color: #eee;color:#333;"">
            <td colspan="3">
                <div align="right">
                    <strong>Subtotal FORMACION RECURSOS HUMANOS (max. {{$maxGrupoSiguiente}})</strong>
                </div>
                <input type="hidden"  name="maxgrupoitemG2" id="maxgrupoitemG2" value="{{$maxGrupoSiguiente}}"></td><td style="text-align:right">
                <strong><span id="spangrupoitemG2" ></span></strong><div id="divgrupoitemG2" class="fValidator-a"></div>
            </td>
            </tr>
        @endif




        </tbody></table>
@if($planilla->motivo !='A')
        @php
            $primerplans = $planMaximos->first();

            //dd($primerplans);
        @endphp

        <table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666;margin-bottom:5px">
            <tbody>
            <input type="hidden" name="cantplans" id="cantplans" value="{{$planMaximos->count()}}">
            <tr style="border-style: solid; border-width: 1px; border-color: #666">
                @php
                    $submax = 0;
                    $submax2 = 0;
                    $sub = 0;
                    $j = 0;
                    $max = 0;
                    $maxGrupoSiguiente = 0;//como voy recorrrelo todo aprovecho a calcular el maximo de los RRHH
                @endphp
                @foreach($planMaximos as $planMaximo)



                        <!-- Verificar si se cambia de grupo para mostrar el subtotal -->



            @php
                $puntajeplan = $evaluacion->puntaje_plans->where('viaje_evaluacion_planilla_plan_max_id', $planMaximo->id)->first();
                $puntaje = (($puntajeplan)&&($puntajeplan->puntaje))?(int)$puntajeplan->puntaje:'';


                $step = '1';

                $input = '<td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;"><input type="number" step="'.$step.'" min="0" size="5" name="puntajeplan'.$loop->index.'" id="puntajeplan'.$loop->index.'" value="'.$puntaje.'" onblur="sumar_total();"></td><td style="background-color: #eee;color:#333; width:80px; text-align: right; vertical-align:middle;"><strong><span id="spanpuntajeplan'.$loop->index.'" ></span></strong><div id="divpuntajeplan'.$loop->index.'" class="fValidator-a"></div><input name="valorplan'.$loop->index.'" id="valorplan'.$loop->index.'" type="hidden" value=""/></td>';
                $c_u=' c/u';

                 $hasta =$planMaximo->maximo;

                $descripcion = ($planilla->motivo != 'C')?'PLAN DE TRABAJO':'PLAN DE TRABAJO Y CV DEL VISITANTE<br>';
                $descripcion_anexo=($planilla->motivo != 'C')?'<ul><li>Objetivo general de la estadía.</li><li>Objetivos específicos de la estadía.</li><li>Plan de trabajo de investigación a realizar en el período.</li><li>Cronograma</li></ul>':'';
            @endphp
            @if($primeritems->id == $itemMaximo->id)
                <td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;" rowspan="{{($planilla->iterador1 + 4)}}">PROD. ULTIMOS 5 AÑOS {{($planilla->motivo!='C')?'':' DEL SOLICITANTE'}}<br><strong>Max. {{$itemMaximo->padre_maximo}}pt.</strong></td>
            @endif
                    <td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;" rowspan="2"><strong>Max. {{$planMaximo->maximo}}pt.</strong></td>
            <td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;width: 60%;">{!! $descripcion !!}</td>
            <td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;width: 20%;">
                <input type="hidden"  name="id_plan{{$loop->index}}" id="id_plan{{$loop->index}}" value="{{$planMaximo->id}}">
                <input type="hidden"  name="maxplan{{$loop->index}}" id="maxplan{{$loop->index}}" value="{{$planMaximo->maximo}}">



            </td>{!! $input !!}
            </tr>

            <tr style="border-style: solid; border-width: 1px; border-color: #666"><td style="background-color: #eee;color:#333;text-align:left;">Justificación del puntaje del ítem superior</td>
                <td style="background-color: #fff;color:#333; text-align:left;" colspan="3"><textarea name="justificacionplan{{$loop->index}}" id="justificacionplan{{$loop->index}}" style="width:100%" rows="3">{{($puntajeplan)?$puntajeplan->justificacion:''}}</textarea>

            </tr>



            @endforeach






            </tbody></table>
@endif

    @php
        $primereventos = $eventoMaximos->first();

    @endphp

    <table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666;margin-bottom:5px">
        <tbody>
        <input type="hidden" name="canteventos" id="canteventos" value="{{$eventoMaximos->count()}}">
        <tr style="border-style: solid; border-width: 1px; border-color: #666">
            @php
                $submax = 0;

                $sub = 0;
                $j = 0;
                $max = 0;

                    $checkedUnidad = ($unidadAprobada) ?' CHECKED ':'';


            @endphp
            @foreach($eventoMaximos as $eventoMaximo)



                    <!-- Verificar si se cambia de grupo para mostrar el subtotal -->
        @if ($submax != $eventoMaximo->evaluacion_grupo_id)
            @php
                //echo $eventoMaximos[$loop->index]->grupo_maximo;
            @endphp

            @if ($loop->index != 0 && $eventoMaximos[$loop->index - 1]->grupo_maximo != 0)
                <tr style="background-color: #eee; color: #333;">
                    <td></td>
                    <td colspan="2">
                        <div align="right">
                            <strong>Subtotal (max. {{ $eventoMaximos[$loop->index - 1]->grupo_maximo }})</strong>
                        </div>
                        <input type="hidden"
                               name="maxgrupoevento{{ $eventoMaximos[$loop->index - 1]->evaluacion_grupo_id }}"
                               id="maxgrupoevento{{ $eventoMaximos[$loop->index - 1]->evaluacion_grupo_id }}"
                               value="{{ $eventoMaximos[$loop->index - 1]->grupo_maximo }}" />
                        <strong>
                            <div id="divgrupoeventos{{ $eventoMaximos[$loop->index - 1]->evaluacion_grupo_id }}" class="fValidator-a"></div>
                        </strong>
                    </td>
                    <td style="text-align:right"><strong><span id="spangrupoevento{{ $eventoMaximos[$loop->index - 1]->evaluacion_grupo_id }}" ></span></strong><div id="divgrupoevento{{ $eventoMaximos[$loop->index - 1]->evaluacion_grupo_id }}" class="fValidator-a"></div></td>
                </tr>
            @endif

            @php
                $submax = $eventoMaximo->evaluacion_grupo_id;
            @endphp
        @endif


        @php
            $puntajeevento = $evaluacion->puntaje_eventos->where('viaje_evaluacion_planilla_evento_max_id', $eventoMaximo->id)->first();

            $puntaje = (($puntajeevento)&&($puntajeevento->puntaje))?(int)$puntajeevento->puntaje:'';
            $cantidad = (($puntajeevento)&&($puntajeevento->cantidad))?$puntajeevento->cantidad:'';
           $step = (($eventoMaximo->maximo!=0)&&($eventoMaximo->minimo==$eventoMaximo->maximo))?((($eventoMaximo->minimo==$eventoMaximo->maximo))?'1':'0.01'):'0.01';



           $puntaje=($puntajeevento)?(int) $puntajeevento->puntaje:'';

           $checked = (($eventoMaximo->minimo)&&($puntaje)&&($puntajeevento->id)&&($puntajeevento->viaje_evaluacion_planilla_evento_max_id==$eventoMaximo->id))?' CHECKED ':'';
            $disabled =(strchr($eventoMaximo->evento_nombre,'(Ord. 284)'))?' DISABLED ':'';
            $checked = (strchr($eventoMaximo->evento_nombre,'(Ord. 284)'))?$checkedUnidad:$checked;

            $input = ($eventoMaximo->minimo!=0)?'<td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;"><input type="checkbox" size="5" name="puntajeevento'.$loop->index.'" id="puntajeevento'.$loop->index.'" value="'.$eventoMaximo->maximo.'"'.$checked.$disabled.' onclick="sumar_total();"></td><td style="background-color: #eee;color:#333; width:80px; text-align: right; vertical-align:middle;"><span id="spanpuntajeevento'.$loop->index.'" ></span><div id="divpuntajeevento'.$loop->index.'" class="fValidator-a"></div><input name="valorevento'.$loop->index.'" id="valorevento'.$loop->index.'" type="hidden" value=""/></td>':'<td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;"></td><td style="background-color: #eee;color:#333;" align="right"><input type="number" step="'.$step.'" min="0" size="5" name="puntajeevento'.$loop->index.'" id="puntajeevento'.$loop->index.'" value="'.$puntaje.'" onblur="validarCampos2(' . $loop->index . ', \'evento\'); sumar_total();"><div id="divpuntajeevento'.$loop->index.'" class="fValidator-a"></div><input name="valorevento'.$loop->index.'" id="valorevento'.$loop->index.'" type="hidden" value=""/></td>';
            $c_u=' c/u';

		 	$hasta = (($eventoMaximo->maximo!=0)&&($eventoMaximo->minimo==$eventoMaximo->maximo))?((($eventoMaximo->minimo==$eventoMaximo->maximo))?$eventoMaximo->maximo. ' pt.':$eventoMaximo->maximo. $c_u):(($eventoMaximo->minimo!=$eventoMaximo->maximo)?'Hasta '.$eventoMaximo->maximo:'Hasta '.$eventoMaximo->maximo);
		 	//$hasta =$eventoMaximo->maximo. $c_u;
            $descripcion = str_replace('#puntaje#', '<B>'.$eventoMaximo->maximo.' puntos</B>',$eventoMaximo->evento_nombre);
        @endphp
        @if($primereventos->id == $eventoMaximo->id)
            <td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;" rowspan="{{($eventoMaximos->count() + 3)}}"><strong>Max. {{$eventoMaximo->grupo_maximo}}pt.</strong></td>
        @endif



        <td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;width: 60%;">{!! $descripcion !!}</td>
        <td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;width: 20%;">
            <input type="hidden"  name="id_evento{{$loop->index}}" id="id_evento{{$loop->index}}" value="{{$eventoMaximo->id}}">
            <input type="hidden"  name="maxevento{{$loop->index}}" id="maxevento{{$loop->index}}" value="{{$eventoMaximo->maximo}}">

            <input type="hidden"  name="minevento{{$loop->index}}" id="minevento{{$loop->index}}" value="{{$eventoMaximo->minimo}}">
            <input type="hidden"  name="evento_evaluacion_grupo_id{{$loop->index}}" id="evento_evaluacion_grupo_id{{$loop->index}}" value="{{$eventoMaximo->evaluacion_grupo_id}}">{!! $hasta !!}
        </td>{!! $input !!}
        </tr>
        @if($eventoMaximo->minimo==0)
            <tr style="border-style: solid; border-width: 1px; border-color: #666"><td style="background-color: #eee;color:#333;text-align:left;">Justificación del puntaje del ítem superior</td>
                <td style="background-color: #fff;color:#333; text-align:left;" colspan="3"><textarea name="justificacionevento{{$loop->index}}" id="justificacionevento{{$loop->index}}" style="width:100%" rows="3">{{($puntajeevento)?$puntajeevento->justificacion:''}}</textarea>

            </tr>
        @endif
        <!--
        </tr>-->



        @endforeach

        @php
            $ultimoeventoMaximo=$eventoMaximos->last();
            //dd($ultimoeventoMaximo);

        @endphp
        @if($ultimoeventoMaximo->grupo_maximo != 0)
            <tr style="background-color: #eee;color:#333;"">
            <td></td>
            <td colspan="3">
                <div align="right"><strong>Subtotal (max. {{$ultimoeventoMaximo->grupo_maximo}})</strong></div>
                <input type="hidden"  name="maxgrupoevento{{$ultimoeventoMaximo->evaluacion_grupo_id}}" id="maxgrupoevento{{$ultimoeventoMaximo->evaluacion_grupo_id}}" value="{{$ultimoeventoMaximo->grupo_maximo}}">
            </td>
            <td style="text-align:right"><strong><span id="spangrupoevento{{$ultimoeventoMaximo->evaluacion_grupo_id}}" ></span></strong><div id="divgrupoevento{{$ultimoeventoMaximo->evaluacion_grupo_id}}" class="fValidator-a"></div></td>
            </tr>
        @endif





        </tbody></table>
    <table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666;margin-bottom:5px">
        <tbody><tr style="border-style: solid; border-width: 1px; border-color: #666">
            <td>
                <table style="width:100%">
                    <tbody><tr>
                        <td><div align="right">


                                <strong> Total (max. <span>{{$planilla->maximo}}</span> )</strong></div></td>
                    </tr>
                    </tbody></table>                        </td>
            <td style="background-color: #eee;color:#333; width:80px"><div align="right"><strong><span id="spanTotal">
                            0</span></strong></div>

            {{Form::hidden('puntaje',0,['id' => 'puntaje'])}}
            </td>
        </tr>
        </tbody></table>
    <div class="col-md-12">

        <div class="form-group">
            {{Form::label('observaciones', 'Observaciones')}}
            {{Form::textarea('observaciones', $evaluacion->observaciones, ['class' => 'form-control'])}}

        </div>
    </div>
</fieldset>


                                        <!-- Añade otros ítems según tu lógica -->


                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                        <a href="{{ route('viajes.index') }}" class="btn btn-warning">Volver</a>

                                    </div>

                            </div>
                        </form>
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col-->
            </div>

            <!-- ./row -->
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
    <!-- SlimScroll -->
    <script src="{{ asset('bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('bower_components/fastclick/lib/fastclick.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('dist/js/demo.js') }}"></script>
    <!-- jQuery UI -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script src="{{ asset('dist/js/confirm-exit.js') }}"></script>
    <!-- page script -->
    <script>
        $(document).ready(function () {


            $( 'input[name="viaje_planilla_categoria_max_id"]:radio' ).change(function() {
                if(this.id=='viaje_planilla_categoria_max_id0'){
                    $('#categoria').prop('checked', true);

                }
                else{
                    $('#categoria').prop('checked', false);
                }
            });

        });

        function sumar_total(){
            //$("#{form_id}_input_submit_ajax").show();
            var categoria = $("input[name='viaje_planilla_categoria_max_id']:checked").val();


            if (categoria != null){
                var categoriaArray = categoria.split('-');
            }
            else{
                var categoriaArray = [];
                categoriaArray[1]=0;
            }

            $('#spanCategoria').text(formatDec(categoriaArray[1],2));

            var total = 0;
            var categoria = $('#categoria');
            var factor = $('#factor');
            var spanF = $('#spanF');
            var totalAntAcad=0;
            var submax=0;

            var subpuntaje=parseFloat(total);


            total = parseFloat(total) + parseFloat($('#spanCategoria').text());

            var cargo = $("input[name='cargomaximo']:checked").val();


            if (cargo != null){
                var cargoArray = cargo.split('-');
            }
            else{
                var cargoArray = [];
                cargoArray[1]=0;
            }

            $('#spanCargo').text(formatDec(cargoArray[1],2));


            total = parseFloat(total) + parseFloat($('#spanCargo').text());



            cantidad = $('#iterador1');
            var totalItem=0;
            var submax=0;
            var subpuntaje=0;


            for (var i=0; i<cantidad.val(); i++){

                cant = $('#cantitem'+i);

                tope = $('#topeitem'+i);
                puntajeElem = $('#puntajeitem'+i);
                item_evaluacion_grupo_id = $('#item_evaluacion_grupo_id'+i);
                max = $('#maxitem'+i);

                min = $('#minitem'+i);
                div = $('#divpuntajeitem'+i);
                div.text('');
                span = $('#spanpuntajeitem'+i);
                valor=$('#valoritem'+i);

                if (item_evaluacion_grupo_id.length > 0){
                    maxpuntajegrupo = $('#maxgrupoitem'+item_evaluacion_grupo_id.val());
                }
                if (cant.length > 0){
                    cantidadItem = (cant.val()!='')?parseFloat(cant.val()):0;

                    if(puntajeElem.attr('type') == "checkbox"){
                        puntaje = (puntajeElem.attr("checked"))?parseFloat(puntajeElem.val()):0;
                        //console.log(puntaje);

                    }
                    else {
                        puntaje = (puntajeElem.val() != '') ? parseFloat(puntajeElem.val()) : 0;

                    }
                    if((cantidadItem!=0)&&(puntaje==0)){
                        /*div.text('Falta el puntaje');*/


                    }
                    else{


                        /*if(puntaje>(cantidadItem*parseFloat(max.val()))){*/

                            /*div.text('Puntaje excedido');*/



                        //}
                        $pasoElTope = 0;
                        if(tope.val() != 0){

                            if(puntaje>(tope.val())){

                                /*div.text('Puntaje excedido no puede superar a '+ tope.val());*/
                                $pasoElTope = 1;


                            }


                        }
                   }
                }


                if ((puntajeElem.length > 0)){
                    if(puntajeElem.attr('type') == "checkbox"){
                        puntaje = (puntajeElem.attr("checked"))?parseFloat(puntajeElem.val()):0;

                    }
                    else{

                    }
                        puntaje = (puntajeElem.val()!='')?parseFloat(puntajeElem.val()):0;
                    //console.log(tope.val()+' - '+puntaje);
                    if((tope.val() != 0)&&(puntajeElem.attr('type') != "checkbox")&&(puntaje>parseFloat(tope.val()))) {
                        if(!$pasoElTope){
                            div.text('Excedido, toma el max.');
                            span.text(tope.val());
                            ptotal = tope.val();
                        }


                    }
                    else{
                        if((puntajeElem.attr('type') == "checkbox")||(max.val() == 0)){
                            span.text(formatDec(puntaje,2));
                            ptotal = puntaje;

                        }
                        else{

                            if (cant.length > 0){

                                ptotal = puntaje;
                            }
                            else{
                                if((tope.val() != 0)&&(puntaje*max.val()>parseFloat(tope.val()))) {

                                        div.text('Excedido, toma el max.');
                                        span.text(tope.val());
                                        ptotal = tope.val();



                                }
                                else{
                                    span.text(formatDec(puntaje*max.val(),2));
                                    ptotal = puntaje*max.val();
                                }






                            }
                            valor.val(parseFloat(ptotal));

                        }

                        //div.text('');

                    }
                    //alert('i: '+i+' - submax: '+submax+' - item_evaluacion_grupo_id.val(): '+item_evaluacion_grupo_id.val());
                    if ((i==0)||(submax==item_evaluacion_grupo_id.val() )){

                        subpuntaje = parseFloat(subpuntaje) + parseFloat(ptotal);


                    }
                    else {

                        item_evaluacion_grupo_id_ant = $('#item_evaluacion_grupo_id'+(i-1));

                        if (item_evaluacion_grupo_id_ant.length > 0){
                            maxpuntajegrupoant = $('#maxgrupoitem'+item_evaluacion_grupo_id_ant.val());
                            divgrupo = $('#divgrupoitem'+item_evaluacion_grupo_id_ant.val());
                            spangrupo = $('#spangrupoitem'+item_evaluacion_grupo_id_ant.val());
                        }

                        if(subpuntaje > maxpuntajegrupoant.val()){
                            totalItem=parseFloat(totalItem)+parseFloat(maxpuntajegrupoant.val());
                            spangrupo.text(parseFloat(maxpuntajegrupoant.val()));
                            divgrupo.text('Excedido, toma el max.');

                        }
                        else{
                            totalItem=parseFloat(totalItem)+parseFloat(subpuntaje);
                            spangrupo.text(formatDec(parseFloat(subpuntaje),2));
                            divgrupo.text('');

                        }

                        subpuntaje=ptotal;
                    }

                    submax=item_evaluacion_grupo_id.val();



                }
                //}
                //}

            }
            item_evaluacion_grupo_id_ant = $('#item_evaluacion_grupo_id'+(i-1));

            if (item_evaluacion_grupo_id_ant.length > 0){
                maxpuntajegrupoant = $('#maxgrupoitem'+item_evaluacion_grupo_id_ant.val());
                divgrupo = $('#divgrupoitem'+item_evaluacion_grupo_id_ant.val());
                spangrupo = $('#spangrupoitem'+item_evaluacion_grupo_id_ant.val());
            }


            if(subpuntaje > maxpuntajegrupoant.val()){
                totalItem=parseFloat(totalItem)+parseFloat(maxpuntajegrupoant.val());
                spangrupo.text(parseFloat(maxpuntajegrupoant.val()));
                divgrupo.text('Excedido, toma el max.');
            }
            else{
                totalItem=parseFloat(totalItem)+parseFloat(subpuntaje);
                spangrupo.text(formatDec(parseFloat(subpuntaje),2));
                divgrupo.text('');
            }


            item_evaluacion_grupo_id_ant = $('#maxgrupoitemG1');
            if (item_evaluacion_grupo_id_ant.length > 0){
                maxpuntajegrupoant = $('#maxgrupoitemG1');
                divgrupo = $('#divgrupoitemG1');
                spangrupo = $('#spangrupoitemG1');
            }

            //alert(subpuntaje+' > '+maxpuntajegrupoant.val()+' > '+totalItem)
            if(totalItem > maxpuntajegrupoant.val()){
                totalItem=parseFloat(maxpuntajegrupoant.val());
                spangrupo.text(parseFloat(maxpuntajegrupoant.val()));
                divgrupo.text('Excedido, toma el max.');
            }
            else{
                //totalItem=totalItem+parseFloat(subpuntaje);
                spangrupo.text(formatDec(parseFloat(totalItem),2));
                divgrupo.text('');
            }

            total = parseFloat(total) + parseFloat(totalItem);

            cantidad2 = $('#iterador2');
            var totalItem=0;
            var submax=0;
            var subpuntaje=0;

            for (var i=cantidad.val(); i<parseInt(cantidad2.val()); i++){

                console.log(i);
                cant = $('#cantitem'+i);

                tope = $('#topeitem'+i);
                puntajeElem = $('#puntajeitem'+i);
                item_evaluacion_grupo_id = $('#item_evaluacion_grupo_id'+i);
                max = $('#maxitem'+i);

                min = $('#minitem'+i);
                div = $('#divpuntajeitem'+i);
                div.text('');
                span = $('#spanpuntajeitem'+i);
                valor=$('#valoritem'+i);

                if (item_evaluacion_grupo_id.length > 0){
                    maxpuntajegrupo = $('#maxgrupoitem'+item_evaluacion_grupo_id.val());
                }
                if (cant.length > 0){
                    cantidadItem = (cant.val()!='')?parseFloat(cant.val()):0;

                    if(puntajeElem.attr('type') == "checkbox"){
                        puntaje = (puntajeElem.attr("checked"))?parseFloat(puntajeElem.val()):0;
                        //console.log(puntaje);

                    }
                    else {
                        puntaje = (puntajeElem.val() != '') ? parseFloat(puntajeElem.val()) : 0;

                    }
                    if((cantidadItem!=0)&&(puntaje==0)){
                        /*div.text('Falta el puntaje');*/


                    }
                    else{


                        /*if(puntaje>(cantidadItem*parseFloat(max.val()))){*/

                        /*div.text('Puntaje excedido');*/



                        //}
                        $pasoElTope = 0;
                        if(tope.val() != 0){

                            if(puntaje>(tope.val())){

                                /*div.text('Puntaje excedido no puede superar a '+ tope.val());*/
                                $pasoElTope = 1;


                            }


                        }
                    }
                }


                if ((puntajeElem.length > 0)){
                    if(puntajeElem.attr('type') == "checkbox"){
                        puntaje = (puntajeElem.attr("checked"))?parseFloat(puntajeElem.val()):0;

                    }
                    else{

                    }
                    puntaje = (puntajeElem.val()!='')?parseFloat(puntajeElem.val()):0;
                    //console.log(tope.val()+' - '+puntaje);
                    if((tope.val() != 0)&&(puntajeElem.attr('type') != "checkbox")&&(puntaje>parseFloat(tope.val()))) {
                        if(!$pasoElTope){
                            div.text('Excedido, toma el max.');
                            span.text(tope.val());
                            ptotal = tope.val();
                        }


                    }
                    else{
                        if((puntajeElem.attr('type') == "checkbox")||(max.val() == 0)){
                            span.text(formatDec(puntaje,2));
                            ptotal = puntaje;

                        }
                        else{

                            if (cant.length > 0){

                                ptotal = puntaje;
                            }
                            else{
                                if((tope.val() != 0)&&(puntaje*max.val()>parseFloat(tope.val()))) {

                                    div.text('Excedido, toma el max.');
                                    span.text(tope.val());
                                    ptotal = tope.val();



                                }
                                else{
                                    span.text(formatDec(puntaje*max.val(),2));
                                    ptotal = puntaje*max.val();
                                }






                            }
                            valor.val(parseFloat(ptotal));

                        }

                        //div.text('');

                    }
                    //alert('i: '+i+' - submax: '+submax+' - item_evaluacion_grupo_id.val(): '+item_evaluacion_grupo_id.val());
                    if ((i==10)||(submax==item_evaluacion_grupo_id.val() )){

                        subpuntaje = parseFloat(subpuntaje) + parseFloat(ptotal);


                    }
                    else {

                        item_evaluacion_grupo_id_ant = $('#item_evaluacion_grupo_id'+(i-1));
                        //console.log(item_evaluacion_grupo_id_ant.val());
                        if (item_evaluacion_grupo_id_ant.length > 0){
                            maxpuntajegrupoant = $('#maxgrupoitem'+item_evaluacion_grupo_id_ant.val());
                            divgrupo = $('#divgrupoitem'+item_evaluacion_grupo_id_ant.val());
                            spangrupo = $('#spangrupoitem'+item_evaluacion_grupo_id_ant.val());
                        }

                        if(subpuntaje > maxpuntajegrupoant.val()){
                            totalItem=parseFloat(totalItem)+parseFloat(maxpuntajegrupoant.val());
                            spangrupo.text(parseFloat(maxpuntajegrupoant.val()));
                            divgrupo.text('Excedido, toma el max.');

                        }
                        else{
                            totalItem=parseFloat(totalItem)+parseFloat(subpuntaje);
                            spangrupo.text(formatDec(parseFloat(subpuntaje),2));
                            divgrupo.text('');

                        }

                        subpuntaje=ptotal;
                    }

                    submax=item_evaluacion_grupo_id.val();



                }
                //}
                //}

            }
            item_evaluacion_grupo_id_ant = $('#item_evaluacion_grupo_id'+(i-1));

            if (item_evaluacion_grupo_id_ant.length > 0){
                maxpuntajegrupoant = $('#maxgrupoitem'+item_evaluacion_grupo_id_ant.val());
                divgrupo = $('#divgrupoitem'+item_evaluacion_grupo_id_ant.val());
                spangrupo = $('#spangrupoitem'+item_evaluacion_grupo_id_ant.val());
            }
            //console.log(subpuntaje);
            if(subpuntaje > maxpuntajegrupoant.val()){
                totalItem=parseFloat(totalItem)+parseFloat(maxpuntajegrupoant.val());
                spangrupo.text(parseFloat(maxpuntajegrupoant.val()));
                divgrupo.text('Excedido, toma el max.');
            }
            else{
                totalItem=parseFloat(totalItem)+parseFloat(subpuntaje);
                spangrupo.text(formatDec(parseFloat(subpuntaje),2));
                divgrupo.text('');
            }


            item_evaluacion_grupo_id_ant = $('#maxgrupoitemG2');
            if (item_evaluacion_grupo_id_ant.length > 0){
                maxpuntajegrupoant = $('#maxgrupoitemG2');
                divgrupo = $('#divgrupoitemG2');
                spangrupo = $('#spangrupoitemG2');
            }
            //alert(subpuntaje+' > '+maxpuntajegrupoant.val()+' > '+totalItem)
            if(totalItem > maxpuntajegrupoant.val()){
                totalItem=parseFloat(maxpuntajegrupoant.val());
                spangrupo.text(parseFloat(maxpuntajegrupoant.val()));
                divgrupo.text('Excedido, toma el max.');
            }
            else{
                //totalItem=totalItem+parseFloat(subpuntaje);
                spangrupo.text(formatDec(parseFloat(totalItem),2));
                divgrupo.text('');
            }

            total = parseFloat(total) + parseFloat(totalItem);

            var plan = parseFloat($('#puntajeplan0').val());
            var topeplan = parseFloat($('#maxplan0').val());

            var totalPlan = 0;

            if (isNaN(plan)){
                plan = 0;
            }

            if (!isNaN(topeplan)){
                if(plan<=topeplan){
                    $('#spanpuntajeplan0').text(formatDec(parseFloat(plan),2));
                    $('#divpuntajeplan0').text('');

                    var totalPlan=plan;
                }
                else{
                    $('#spanpuntajeplan0').text(formatDec(parseFloat(topeplan),2));
                    $('#divpuntajeplan0').text('Excedido, toma el max.');
                    var totalPlan =topeplan;
                }
            }
            total = parseFloat(total) + parseFloat(totalPlan);


            cantidad = $('#canteventos')
            var totalEvento=0;
            submax=0;
            subpuntaje=0;
            var evento;
            for (var i=0; i<cantidad.val(); i++){
                min = $('#minevento'+i);

                puntajeElem = $('#puntajeevento'+i);
                //console.log(puntajeElem);
                evento_evaluacion_grupo_id = $('#evento_evaluacion_grupo_id'+i);
                max = $('#maxevento'+i);
                min = $('#minevento'+i);
                div = $('#divpuntajeevento'+i);
                div.text('');
                span = $('#spanpuntajeevento'+i);
                valor=$('#valorevento'+i);
                if (evento_evaluacion_grupo_id != null){

                    maxpuntajegrupo = $('#maxgrupoeventos'+evento_evaluacion_grupo_id.val());
                }
                if (puntajeElem != null){


                    if (puntajeElem.attr('type') == "checkbox") {
                        puntaje = (puntajeElem.prop("checked")) ? parseFloat(puntajeElem.val()) : 0;
                        //console.log(puntajeElem.prop("checked"));
                    }
                    else if(puntajeElem.attr('type') == "radio"){
                        evento = $('#maxevento'+i);
                        puntaje = (puntajeElem.attr("checked"))?parseFloat(puntajeElem.val()):0;

                    }
                    else {
                        puntaje = (puntajeElem.val()!='')?parseFloat(puntajeElem.val()):0;
                    }
                    if((puntajeElem.attr('type') == "checkbox")||(max.val() == 0)){
                        span.text(formatDec(puntaje,2));
                        ptotal = puntaje;
                    }
                    else{
                        span.text(formatDec(puntaje,2));
                        ptotal = puntaje;
                        valor.val(parseFloat(ptotal));

                    }

                    div.text('');

                    if ((i==0)||(submax==evento_evaluacion_grupo_id.val() )){
                        subpuntaje = parseFloat(subpuntaje) + parseFloat(ptotal);
                    }
                    else {
                        evento_evaluacion_grupo_idant = $('#evento_evaluacion_grupo_id'+(i-1));

                        if (evento_evaluacion_grupo_idant != null){
                            maxpuntajegrupoant = $('#maxgrupoevento'+evento_evaluacion_grupo_idant.val());
                            divgrupo = $('#divgrupoevento'+evento_evaluacion_grupo_idant.val());
                            spangrupo = $('#spangrupoevento'+evento_evaluacion_grupo_idant.val());
                        }

                        if(subpuntaje > maxpuntajegrupoant.val()){
                            totalEvento=parseFloat(totalEvento)+parseFloat(maxpuntajegrupoant.val());
                            spangrupo.text(parseFloat(maxpuntajegrupoant.val()));
                            divgrupo.text('Excedido, toma el max.');
                        }
                        else{
                            totalEvento=parseFloat(totalEvento)+parseFloat(subpuntaje);
                            spangrupo.text(formatDec(parseFloat(subpuntaje),2));
                            divgrupo.text('');
                        }
                        subpuntaje=ptotal;
                    }
                    submax=evento_evaluacion_grupo_id.val();



                }
                //}
                //}

            }
            evento_evaluacion_grupo_id_ant = $('#evento_evaluacion_grupo_id'+(i-1));

            if (evento_evaluacion_grupo_id_ant != null){

                maxpuntajegrupoant = $('#maxgrupoevento'+evento_evaluacion_grupo_id_ant.val());
                divgrupo = $('#divgrupoevento'+evento_evaluacion_grupo_id_ant.val());
                spangrupo = $('#spangrupoevento'+evento_evaluacion_grupo_id_ant.val());

            }

            if(subpuntaje > maxpuntajegrupoant.val()){

                totalEvento=parseFloat(totalEvento)+parseFloat(maxpuntajegrupoant.val());
                //console.log('1 '+totalEvento);
                spangrupo.text(parseFloat(maxpuntajegrupoant.val()));
                divgrupo.text('Excedido, toma el max.');

            }
            else{

                totalEvento=parseFloat(totalEvento)+parseFloat(subpuntaje);
                //console.log('2 '+totalEvento);
                spangrupo.text(formatDec(parseFloat(subpuntaje),2));
                divgrupo.text('');
            }
            evento_evaluacion_grupo_id_ant = $('#maxgrupoevento');
            if (evento_evaluacion_grupo_id_ant.length > 0){
                maxpuntajegrupoant = $('#maxgrupoevento');
                divgrupo = $('#divgrupoevento');
                spangrupo = $('#spangrupoevento');
            }

            if(totalEvento > maxpuntajegrupoant.val()){
                totalEvento=parseFloat(maxpuntajegrupoant.val());
                //console.log('3 '+totalEvento);
                spangrupo.text(parseFloat(maxpuntajegrupoant.val()));
                divgrupo.text('Excedido, toma el max.');
            }
            else{

                spangrupo.text(formatDec(parseFloat(totalEvento),2));
                divgrupo.text('');
            }
            //console.log('afuera '+totalEvento);
            total = parseFloat(total) + parseFloat(totalEvento);




            $('#puntaje').val(total);
            $('#spanTotal').text(formatDec(total,2));
        }


        function formatDec(number, decimals) {
            if (!isNaN(number) && !isNaN(decimals)) {
                return parseFloat(number).toFixed(decimals);
            }
            return number;
        }

        function validarCampos(index, tipo) {
            // Accede a los elementos DOM directamente usando .get(0) o [0]
            var cantidad = $('#cant' + tipo + index).get(0);
            var puntaje = $('#puntaje' + tipo + index).get(0);
            var max = parseFloat($('#max' + tipo + index).val());
            var tope = parseFloat($('#tope' + tipo + index).val());



            // Restablece los mensajes de validación
            cantidad.setCustomValidity('');
            puntaje.setCustomValidity('');

            // Verificar si ambos campos están vacíos o llenos
            if ((cantidad.value.trim() === '' && puntaje.value.trim() !== '') ||
                (cantidad.value.trim() !== '' && puntaje.value.trim() === '')) {

                puntaje.setCustomValidity('Falta el puntaje');

            }


            if(tope===0){
                // Verificar si el puntaje excede el valor máximo permitido
                if (parseFloat(puntaje.value) > (parseFloat(cantidad.value) * max)) {
                    puntaje.setCustomValidity('Puntaje excedido. No puede superar ' + (max * cantidad.value));
                }
            }
            else{
                //console.log(puntaje.value +' > '+tope);
                if (parseFloat(puntaje.value) > tope) {
                    puntaje.setCustomValidity('Puntaje excedido. No puede superar ' + tope);
                }
            }





            // Forzar la validación de HTML5
            cantidad.reportValidity();
            puntaje.reportValidity();
        }

        function validarCampos2(index, tipo) {
            // Accede a los elementos DOM directamente usando .get(0) o [0]

            var puntaje = $('#puntaje' + tipo + index).get(0);
            var max = parseFloat($('#max' + tipo + index).val());





            puntaje.setCustomValidity('');

            // Verificar si ambos campos están vacíos o llenos
            if (puntaje.value.trim() === '') {

                puntaje.setCustomValidity('Falta el puntaje');

            }



            // Verificar si el puntaje excede el valor máximo permitido
            if (parseFloat(puntaje.value) > (parseFloat(max))) {
                puntaje.setCustomValidity('Puntaje excedido. No puede superar ' + (max));
            }







            puntaje.reportValidity();
        }

        function prepararEnvio() {
            // Habilitar todos los inputs deshabilitados antes de enviar
            $('#formEvaluar').find('input:disabled').prop('disabled', false);

            // Llamar a la función de validación del formulario
           // if (!validarFormulario()) {
               // return false; // Si hay un error, prevenir el envío
           // }

            return true; // Permitir el envío del formulario
        }

        function validarFormulario() {
            // Puedes agregar aquí lógica para verificar que no haya errores de validación
            // Por ejemplo, revisando si hay mensajes de error en los divs correspondientes.
            let valid = true;

            // Verifica si hay mensajes de error
            $('.fValidator-a').each(function() {
                if ($(this).text().trim() !== '') {
                    valid = false; // Si hay un mensaje de error, el formulario no es válido
                }
            });

            return valid; // Devuelve el estado de validez
        }

        $(document).ready(sumar_total);

    </script>

@endsection
