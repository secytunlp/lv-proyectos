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
                <i class="fa fa-clipboard-check" aria-hidden="true"></i>Evaluación Jóvenes Investigadores
                <small>Evaluar solicitud</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('jovens.index') }}">Jóvenes Investigadores</a></li>

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
                        <form role="form" id="formEvaluar" action="{{ route('joven_evaluacions.saveEvaluar',$evaluacion->id) }}" method="post" enctype="multipart/form-data" onsubmit="return prepararEnvio();">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            {{Form::hidden('joven_evaluacion_planilla_id', $planilla->id)}}
                            <div class="box-body">
                                @include('includes.messages')
                                <a name="ancla" id="ancla"></a>
<fieldset>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Solicitante:</label>
                                                    <span>{{ $joven->investigador->persona->apellido }} {{ $joven->investigador->persona->nombre }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>U. Académica:</label>
                                                    <span>{{ $facultad->nombre }}</span>
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
    @php
        $primerPosgrado = $posgradoMaximos->first();
        $iterarPosgrado = 0;
        $primerAntAcad = $antAcadMaximos->first();
        $iterarAntAcad = 0;
        //dd($yearEgresado);
        if($yearEgresado <6 ){
					$factor = 1;
				}
        else if(($yearEgresado >= 6)&&($yearEgresado < 7)){
            $factor = 0.9;
        }
        else if(($yearEgresado >= 7)&&($yearEgresado < 8)){
            $factor = 0.8;
        }
        else if(($yearEgresado >= 8)&&($yearEgresado < 9)){
            $factor = 0.7;
        }
        else if(($yearEgresado >= 9)&&($yearEgresado < 10)){
            $factor = 0.6;
        }
        else if($yearEgresado >= 10){
            $factor = 0.5;
        }

    @endphp
                                    <table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666 ;margin-bottom:5px">
                                        <tbody><tr style="border-style: solid; border-width: 1px; border-color: #666">
                                            <td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;">
                                                {{$primerPosgrado->nombre}}</td> <td style="background-color: #eee;color:#333;" colspan="6"><div align="center"><strong>ANTECEDENTES  ACADÉMICOS DEL SOLICITANTE</strong></div></td>
                                        </tr>
                                        <tr style="border-style: solid; border-width: 1px; border-color: #666">
                                            <td style="background-color: #eee;color:#333; width:80px">&nbsp;</td>
                                            <td style="background-color: #eee;color:#333;" colspan="6"><div align="left">Título de Posgrado:
                                                    <strong>hasta {{$maxPosgrado}} puntos</strong> más por el título de posgrado obtenido en la
                                                    especialidad (si lo hubiere) </div></td>
                                        </tr>
                                        <tr style="border-style: solid; border-width: 1px; border-color: #666"><td style="background-color: #eee;color:#333; width:80px">{{$primerPosgrado->nombre}}1<br><strong>Max. {{$maxPosgrado}}pt.</strong></td><td style="background-color: #fff; border-width: 0px; border-color: #fff">
                                                @foreach($posgradoMaximos as $posgradoMaximo)
                                                    @php
                                                        $puntajePosgrado = $evaluacion->puntaje_posgrados->first();
                                                        $checked = (($puntajePosgrado)&&($puntajePosgrado->joven_evaluacion_planilla_posgrado_max_id==$posgradoMaximo->id))?' CHECKED ':'';
                                                    @endphp


                                                    <input name="joven_planilla_posgrado_max_id" id="joven_planilla_posgrado_max_id{{$iterarPosgrado}}" type="radio" value="{{$posgradoMaximo->id}}-{{$posgradoMaximo->maximo}}" onclick="sumar_total();"{{$checked}}'>
                                                    <input name="posgrado_maximo{{$iterarPosgrado}}" id="posgrado_maximo{{$iterarPosgrado}}" type="hidden" value="{{$posgradoMaximo->maximo}}">{{$posgradoMaximo->posgrado_nombre}} ({{$posgradoMaximo->maximo}}pt.)</td><td style="background-color: #fff; border-width: 0px; border-color: #fff">
                                                @php

                                                    $iterarPosgrado ++;
                                                @endphp
                                                @endforeach

                                               <td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;"><div align="right"><input name="posgrado_puntaje" id="posgrado_puntaje" type="hidden" value="0"><span id="spanPosgrado">0</span></div></td></tr></tbody></table>

    <table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666;margin-bottom:5px">
        <tbody>
        <input type="hidden" name="cantantacad" id="cantantacad" value="{{$antAcadMaximos->count()}}">
        @foreach($antAcadMaximos as $antAcadMaximo)
            @php
                $tope = (($antAcadMaximo->maximo!=0)&&($antAcadMaximo->minimo==$antAcadMaximo->maximo))?((($antAcadMaximo->minimo==$antAcadMaximo->tope)&&($antAcadMaximo->minimo==$antAcadMaximo->maximo))?'':'<strong>Max. '.$antAcadMaximo->tope.'pt.</strong>'):'<strong>Max. '.$antAcadMaximo->tope.'pt.</strong>';
                $hasta = (($antAcadMaximo->maximo!=0)&&($antAcadMaximo->minimo==$antAcadMaximo->maximo))?((($antAcadMaximo->minimo==$antAcadMaximo->tope)&&($antAcadMaximo->minimo==$antAcadMaximo->maximo))?$antAcadMaximo->maximo. ' pt.':$antAcadMaximo->maximo. ' c/u'):'Hasta '.$antAcadMaximo->tope;
                $step = (($antAcadMaximo->maximo!=0)&&($antAcadMaximo->minimo==$antAcadMaximo->maximo))?'1':'0.01';
                $puntajeAntAcad = $evaluacion->puntaje_ant_acads->where('joven_evaluacion_planilla_ant_acad_max_id', $antAcadMaximo->id)->first();

                $puntaje=($puntajeAntAcad)?(int) $puntajeAntAcad->puntaje:'';

                $checked = (($antAcadMaximo->minimo)&&($puntaje)&&($puntajeAntAcad->id)&&($puntajeAntAcad->joven_evaluacion_planilla_ant_acad_max_id==$antAcadMaximo->id))?' CHECKED ':'';
                /*if ($puntajeAntAcad->id==3){
                    dd($antAcadMaximo,$puntajeAntAcad,$puntaje,$checked);
                }*/
            @endphp
            @if (($antAcadMaximo->minimo==0)&&($antAcadMaximo->tope==0)&&($antAcadMaximo->minimo==$antAcadMaximo->maximo))
                @php
                    $checkedposgrado = ($puntaje==2)?' CHECKED ':'';
                     $puntaje=($puntaje==2)?1:$puntaje;
                @endphp
                <tr>
                    <td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;">{{$antAcadMaximo->grupo_nombre}}{{$iterarAntAcad+2}}<br></td>
                    <td colspan="4" style="background-color: #eee;color:#333;text-align: left;">
                        Se aplicará un "factor de eficiencia" F que multiplicará
                        al resultado de la suma de los puntajes correspondientes a A.1), A.2) y A.3): Llamando G al número de años transcurridos
                        desde la obtención del título de grado. <br>Si ya obtuvo el grado académico superior de la especialidad, o G&lt;6 entonces
                        F=1 <br>Si aún no obtuvo el grado académico superior de la especialidad y G&gt;=6 entonces: SI 6=&lt; G &lt;7 entonces F=0.9 -- SI
                        7=&lt; G &lt;8 entonces F=0.8 -- SI 8=&lt; G &lt;9 entonces F=0.7 -- SI 9=&lt; G &lt;10 entonces F=0.6 -- SI G&gt;=10 entonces F=0.5</td>
                </tr>
                <tr>
                    <td style="background-color: #eee;color:#333; width:80px"></td>
                    <td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;">{{$antAcadMaximo->ant_acad_nombre}}</td>
                    <td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;">
                        <input type="hidden" name="maxantacad{{$iterarAntAcad}}" id="maxantacad{{$iterarAntAcad}}" value="{{$antAcadMaximo->maximo}}">
                        <input type="hidden" name="topeantacad{{$iterarAntAcad}}" id="topeantacad{{$iterarAntAcad}}" value="{{$antAcadMaximo->tope}}">
                        <input type="hidden" name="minantacad{{$iterarAntAcad}}" id="minantacad{{$iterarAntAcad}}" value="{{$antAcadMaximo->minimo}}">
                        <input type="hidden" name="ant_acad_evaluacion_grupo_id{{$iterarAntAcad}}" id="ant_acad_evaluacion_grupo_id{{$iterarAntAcad}}" value="{{$antAcadMaximo->evaluacion_grupo_id}}">
                        Posgrado Sup. Especialidad:
                        <input type="checkbox" size="5" name="posgrado{{$iterarAntAcad}}" id="posgrado" value="2" disabled="" onclick="sumar_total();" {{$checkedposgrado}}>
                    </td>
                    <td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;">
                        <input type="hidden" name="factor" id="factor" value="{{$factor}}">
                        <input type="hidden" name="id_ant_acad{{$iterarAntAcad}}" id="id_ant_acad{{$iterarAntAcad}}" value="{{$antAcadMaximo->id}}">
                        <input type="hidden" name="puntajeantacad{{$iterarAntAcad}}" id="puntajeantacad{{$iterarAntAcad}}" value="{{$factor}}">F: <span id="spanF">{{$factor}}</span></td>
                    <td style="background-color: #eee;color:#333; width:80px; text-align: right; vertical-align:middle;"><span id="spanpuntajeantacad{{$iterarAntAcad}}"></span><div id="divpuntajeantacad{{$iterarAntAcad}}" class="fValidator-a" ></div></td>
                </tr>
            @else
                <tr style="border-style: solid; border-width: 1px; border-color: #666">
                    <td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;">{{$antAcadMaximo->grupo_nombre}}{{$iterarAntAcad+2}}<br>{!! $tope !!}</td>
                    @php
                        $input = (($antAcadMaximo->minimo==$antAcadMaximo->tope)&&($antAcadMaximo->minimo==$antAcadMaximo->maximo))?'<input type="checkbox" size="5" name="puntajeantacad'.$iterarAntAcad.'" id="puntajeantacad'.$iterarAntAcad.'" value="'.$antAcadMaximo->maximo.'"'.$checked.' onclick="sumar_total();">':'<input type="number" step="'.$step.'" min="0" size="5" name="puntajeantacad'.$iterarAntAcad.'" id="puntajeantacad'.$iterarAntAcad.'" value="'.$puntaje.'" onblur="sumar_total();" >';
                        $descripcion = str_replace('#puntaje#', '<B>'.$antAcadMaximo->maximo.' puntos</B>',$antAcadMaximo->ant_acad_nombre);
                    @endphp
                    <td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;width: 60%;">{!!  $descripcion !!}</td>
                    <td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;width: 20%">
                        <input type="hidden" name="id_ant_acad{{$iterarAntAcad}}" id="id_ant_acad{{$iterarAntAcad}}" value="{{$antAcadMaximo->id}}">
                        <input type="hidden" name="maxantacad{{$iterarAntAcad}}" id="maxantacad{{$iterarAntAcad}}" value="{{$antAcadMaximo->maximo}}">
                        <input type="hidden" name="topeantacad{{$iterarAntAcad}}" id="topeantacad{{$iterarAntAcad}}" value="{{$antAcadMaximo->tope}}">
                        <input type="hidden" name="minantacad{{$iterarAntAcad}}" id="minantacad{{$iterarAntAcad}}" value="{{$antAcadMaximo->minimo}}">
                        <input type="hidden" name="ant_acad_evaluacion_grupo_id{{$iterarAntAcad}}" id="ant_acad_evaluacion_grupo_id{{$iterarAntAcad}}" value="{{$antAcadMaximo->evaluacion_grupo_id}}">{!! $hasta !!}
                    </td>
                    <td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;">
                        {!! $input !!}
                    </td>
                    <td style="background-color: #eee;color:#333; width:80px; text-align: right; vertical-align:middle;">
                        <span id="spanpuntajeantacad{{$iterarAntAcad}}">0</span>
                        <div id="divpuntajeantacad{{$iterarAntAcad}}" class="fValidator-a"></div>
                        <input name="valorantacad{{$iterarAntAcad}}" id="valorantacad{{$iterarAntAcad}}" type="hidden" value="">
                    </td>
                </tr>
            @endif
            @php

                $iterarAntAcad ++;
            @endphp
        @endforeach
        @php
            $ultimoAntAcadMaximo = $antAcadMaximos->last();
            //dd($ultimoAntAcadMaximo);
        @endphp
        @if($ultimoAntAcadMaximo->grupo_maximo!=0)

            <tr style="background-color: #eee;color:#333;">
                <td></td>
                <td colspan="3">
                    <div align="right">
                        <strong>Subtotal {{$ultimoAntAcadMaximo->grupo_nombre}} (max. {{$ultimoAntAcadMaximo->grupo_maximo}})</strong>
                    </div>
                    <input type="hidden"  name="maxgrupoantacad{{$ultimoAntAcadMaximo->evaluacion_grupo_id}}" id="maxgrupoantacad{{$ultimoAntAcadMaximo->evaluacion_grupo_id}}" value="{{$ultimoAntAcadMaximo->grupo_maximo}}">
                </td>
                <td style="text-align:right">
                    <strong><span id="spangrupoAntacad{{$ultimoAntAcadMaximo->evaluacion_grupo_id}}" ></span></strong>
                    <div id="divgrupoAntacad{{$ultimoAntAcadMaximo->evaluacion_grupo_id}}" class="fValidator-a"></div>
                </td>
            </tr>
        @endif


        </tbody></table>
    <table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666 ;margin-bottom:5px">
        <tbody><tr style="border-style: solid; border-width: 1px; border-color: #666">
            <td style="background-color: #eee;color:#333; width:80px">B</td>
            <td style="background-color: #eee;color:#333;" colspan="5"><div align="center"><strong>ANTECEDENTES DOCENTES</strong></div></td>
        </tr>

        </tbody></table>
    @php
        $cargo_id=0;
        //dd($cargoMaximos);
        switch ($joven->cargo_id) {
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
                <td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;">B1 </td>
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
        $primerOtros = $otroMaximos->first();
        //dd($otroMaximos);
    @endphp
    <table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666 ;margin-bottom:5px">
        <tbody><tr style="border-style: solid; border-width: 1px; border-color: #666">
            <td style="background-color: #eee;color:#333; width:80px">{{$primerOtros->padre_nombre}}</td>
            <td style="background-color: #eee;color:#333;" colspan="5"><div align="center"><strong>OTROS ANTECEDENTES</strong></div></td>
        </tr>

        </tbody></table>
    <table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666;margin-bottom:5px">
        <tbody>
        <input type="hidden" name="cantotros" id="cantotros" value="{{$otroMaximos->count()}}">
        <tr style="border-style: solid; border-width: 1px; border-color: #666">
        @php
            $submax = 0;
            $sub = 0;
            $j = 0;
            $max = 0;
        @endphp
        @foreach($otroMaximos as $otroMaximo)


                <!-- Verificar si se cambia de grupo para mostrar el subtotal -->
            @if ($submax != $otroMaximo->evaluacion_grupo_id)
                @php
                    //echo $otroMaximos[$loop->index]->grupo_maximo;
                @endphp

                @if ($loop->index != 0 && $otroMaximos[$loop->index - 1]->grupo_maximo != 0)
                    <tr style="background-color: #eee; color: #333;">
                        <td></td>
                        <td colspan="3">
                            <div align="right">
                                <strong>Subtotal (max. {{ $otroMaximos[$loop->index - 1]->grupo_maximo }})</strong>
                            </div>
                            <input type="hidden"
                                   name="maxgrupootros{{ $otroMaximos[$loop->index - 1]->evaluacion_grupo_id }}"
                                   id="maxgrupootros{{ $otroMaximos[$loop->index - 1]->evaluacion_grupo_id }}"
                                   value="{{ $otroMaximos[$loop->index - 1]->grupo_maximo }}" />
                            <strong>
                                <div id="divgrupootros{{ $otroMaximos[$loop->index - 1]->evaluacion_grupo_id }}" class="fValidator-a"></div>
                            </strong>
                        </td>
                        <td style="text-align:right"><strong><span id="spangrupoOtro{{ $otroMaximos[$loop->index - 1]->evaluacion_grupo_id }}" ></span></strong><div id="divgrupoOtro{{ $otroMaximos[$loop->index - 1]->evaluacion_grupo_id }}" class="fValidator-a"></div></td>
                    </tr>
                @endif

                @php
                    $submax = $otroMaximo->evaluacion_grupo_id;
                @endphp
            @endif

            <!-- Verificar si hay un nuevo subgrupo -->
            @if ($sub != $otroMaximo->evaluacion_subgrupo_id && $otroMaximo->evaluacion_subgrupo_id)
                @php
                    $j++;
                @endphp
                <tr>
                    <td style="background-color: #eee; color: #333; width: 80px; vertical-align: middle;">{{$otroMaximo->padre_nombre}}{{ $j }}</td>
                    <td style="background-color: #eee; color: #333;" colspan="4">
                        <div align="left">
                            <strong>{{ $otroMaximo->subgrupo_nombre }}</strong>
                        </div>
                    </td>
                </tr>

                @php
                    $sub = $otroMaximo->evaluacion_subgrupo_id;
                @endphp
            @elseif (!$otroMaximo->evaluacion_subgrupo_id)
                @php
                    $j++;
                @endphp
            @endif
            @php
                $puntajeOtro = $evaluacion->puntaje_otros->where('joven_evaluacion_planilla_otro_max_id', $otroMaximo->id)->first();
                $puntaje = (($puntajeOtro)&&($puntajeOtro->puntaje))?(int)$puntajeOtro->puntaje:'';
                $tope = (($otroMaximo->tope==0)||($otroMaximo->tope==$otroMaximo->minimo))?'':'<strong>Max. '.$otroMaximo->tope.'pt.</strong>';
                $hasta = (($otroMaximo->maximo!=0)&&($otroMaximo->minimo==$otroMaximo->maximo))?((($otroMaximo->minimo==$otroMaximo->tope)&&($otroMaximo->minimo==$otroMaximo->maximo))?$otroMaximo->maximo. ' pt.':$otroMaximo->maximo. ' c/u'):'Hasta '.$otroMaximo->tope;
                $step = (($otroMaximo->maximo!=0)&&($otroMaximo->minimo==$otroMaximo->maximo))?'1':'0.01';
                $input = (($otroMaximo->minimo==$otroMaximo->tope)&&($otroMaximo->minimo==$otroMaximo->maximo))?'<input type="checkbox" size="5" name="puntajeotros'.$loop->index.'" id="puntajeotros'.$loop->index.'" value="'.$otroMaximo->maximo.'"'.$checked.' onclick="sumar_total();" DISABLED>':'<input type="number" step="'.$step.'" min="0" size="5" name="puntajeotros'.$loop->index.'" id="puntajeotros'.$loop->index.'" value="'.$puntaje.'" onblur="sumar_total();">';
                $descripcion = str_replace('#puntaje#', '<B>'.$otroMaximo->maximo.' puntos</B>',$otroMaximo->otro_nombre);
            @endphp

            @if($otroMaximo->evaluacion_subgrupo_id)
                <td style="background-color: #eee;color:#333; width:80px">{!! $tope !!}</td>
            @else
                <td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;">{{$otroMaximo->padre_nombre}}{{$j}}<br>{!! $tope !!}</td>
            @endif
            <td style="width:450px;background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;width: 60%;">{!! $descripcion !!}</td>
            <td style="width:120px;background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;width: 20%;">
                <input type="hidden" name="id_otros{{$loop->index}}" id="id_otros{{$loop->index}}" value="{{$otroMaximo->id}}">
                <input type="hidden"  name="maxotros{{$loop->index}}" id="maxotros{{$loop->index}}" value="{{$otroMaximo->maximo}}">
                <input type="hidden"  name="topeotros{{$loop->index}}" id="topeotros{{$loop->index}}" value="{{$otroMaximo->tope}}">
                <input type="hidden"  name="minotros{{$loop->index}}" id="minotros{{$loop->index}}" value="{{$otroMaximo->minimo}}">
                <input type="hidden"  name="otro_evaluacion_grupo_id{{$loop->index}}" id="otro_evaluacion_grupo_id{{$loop->index}}" value="{{$otroMaximo->evaluacion_grupo_id}}">{!! $hasta !!}
            </td>
            <td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;">{!! $input !!}</td>
            <td style="background-color: #eee;color:#333; width:80px; text-align: right; vertical-align:middle;">
                <span id="spanpuntajeotros{{$loop->index}}" ></span>
                <div id="divpuntajeotros{{$loop->index}}" class="fValidator-a"></div>
                <input name="valorotros{{$loop->index}}" id="valorotros{{$loop->index}}" type="hidden" value=""/>
            </td>
        </tr>






        @endforeach
        @php
            $ultimoOtroMaximo = $otroMaximos->last();
            //dd($ultimoAntAcadMaximo);
        @endphp
        @if($ultimoOtroMaximo->grupo_maximo != 0)
            <tr style="background-color: #eee;color:#333;"">
            <td></td>
            <td colspan="3">
                <div align="right"><strong>Subtotal (max. {{$ultimoOtroMaximo->grupo_maximo}})</strong></div>
                <input type="hidden"  name="maxgrupootros{{$ultimoOtroMaximo->evaluacion_grupo_id}}" id="maxgrupootros{{$ultimoOtroMaximo->evaluacion_grupo_id}}" value="{{$ultimoOtroMaximo->grupo_maximo}}">
            </td>
            <td style="text-align:right"><strong><span id="spangrupootros{{$ultimoOtroMaximo->evaluacion_grupo_id}}" ></span></strong><div id="divgrupootros{{$ultimoOtroMaximo->evaluacion_grupo_id}}" class="fValidator-a"></div></td>
            </tr>
        @endif
        @if($ultimoOtroMaximo->padre_id)
            <tr style="background-color: #eee;color:#333;"">
            <td colspan="4">
                <div align="right">
                    <strong>Subtotal {{$ultimoOtroMaximo->padre_nombre}} (max. {{$ultimoOtroMaximo->padre_maximo}})</strong>
                </div>
                <input type="hidden"  name="maxgrupootros" id="maxgrupootros" value="{{$ultimoOtroMaximo->padre_maximo}}"></td><td style="text-align:right">
                <strong><span id="spangrupootros" ></span></strong><div id="divgrupootros" class="fValidator-a"></div>
            </td>
            </tr>
        @endif




        </tbody></table>
    @php
        $primerProduccions = $produccionMaximos->first();
        $periodoActual =intval($joven->periodo->nombre);
    @endphp
    <table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666 ;margin-bottom:5px">
        <tbody>
        <tr style="border-style: solid; border-width: 1px; border-color: #666">
            <td style="background-color: #eee;color:#333; width:80px">{{$primerProduccions->padre_nombre}}</td>
            <td style="background-color: #eee;color:#333;" colspan="5"><div align="center"><strong>FORMACI&Oacute;N DE RR.HH. Y PRODUCCI&Oacute;N CIENT&Iacute;FICA EN LOS ULTIMOS 5 A&Ntilde;OS  ({{$periodoActual-4}}, {{$periodoActual-3}}, {{$periodoActual-2}}, {{$periodoActual-1}}, {{$periodoActual}})</strong></div></td>
        </tr>

        </tbody>
    </table>
    <table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666;margin-bottom:5px">
        <tbody>
        <input type="hidden" name="cantproduccions" id="cantproduccions" value="{{$produccionMaximos->count()}}">
        <tr style="border-style: solid; border-width: 1px; border-color: #666">
    @php
        $submax = 0;
        $sub = 0;
        $j = 0;
        $max = 0;
        if ($unidadAprobada) {
            $checkedUnidad = ' CHECKED ';
        }
    @endphp
    @foreach($produccionMaximos as $produccionMaximo)


        <!-- Verificar si se cambia de grupo para mostrar el subtotal -->
        @if ($submax != $produccionMaximo->evaluacion_grupo_id)
            @php
                //echo $produccionMaximos[$loop->index]->grupo_maximo;
            @endphp

            @if ($loop->index != 0 && $produccionMaximos[$loop->index - 1]->grupo_maximo != 0)
                <tr style="background-color: #eee; color: #333;">
                    <td></td>
                    <td colspan="3">
                        <div align="right">
                            <strong>Subtotal (max. {{ $produccionMaximos[$loop->index - 1]->grupo_maximo }})</strong>
                        </div>
                        <input type="hidden"
                               name="maxgrupoproduccion{{ $produccionMaximos[$loop->index - 1]->evaluacion_grupo_id }}"
                               id="maxgrupoproduccion{{ $produccionMaximos[$loop->index - 1]->evaluacion_grupo_id }}"
                               value="{{ $produccionMaximos[$loop->index - 1]->grupo_maximo }}" />
                        <strong>
                            <div id="divgrupoproduccions{{ $produccionMaximos[$loop->index - 1]->evaluacion_grupo_id }}" class="fValidator-a"></div>
                        </strong>
                    </td>
                    <td style="text-align:right"><strong><span id="spangrupoProduccion{{ $produccionMaximos[$loop->index - 1]->evaluacion_grupo_id }}" ></span></strong><div id="divgrupoProduccion{{ $produccionMaximos[$loop->index - 1]->evaluacion_grupo_id }}" class="fValidator-a"></div></td>
                </tr>
            @endif

            @php
                $submax = $produccionMaximo->evaluacion_grupo_id;
            @endphp
        @endif

        <!-- Verificar si hay un nuevo subgrupo -->
        @if ($sub != $produccionMaximo->evaluacion_subgrupo_id && $produccionMaximo->evaluacion_subgrupo_id)
            @php
                $j++;
            @endphp
            <tr>
                <td style="background-color: #eee; color: #333; width: 80px; vertical-align: middle;">{{$produccionMaximo->padre_nombre}}{{ $j }}</td>
                <td style="background-color: #eee; color: #333;" colspan="4">
                    <div align="left">
                        <strong>{{ $produccionMaximo->subgrupo_nombre }}</strong>
                    </div>
                </td>
            </tr>

            @php
                $sub = $produccionMaximo->evaluacion_subgrupo_id;
            @endphp
        @elseif (!$produccionMaximo->evaluacion_subgrupo_id)
            @php
                $j++;
            @endphp
        @endif
        @php
            $puntajeProduccion = $evaluacion->puntaje_produccions->where('joven_evaluacion_planilla_produccion_max_id', $produccionMaximo->id)->first();
            $puntaje = (($puntajeProduccion)&&($puntajeProduccion->puntaje))?(int)$puntajeProduccion->puntaje:'';
            $cantidad = (($puntajeProduccion)&&($puntajeProduccion->cantidad))?$puntajeProduccion->cantidad:'';
            $tope = (($produccionMaximo->tope==0)||($produccionMaximo->tope==$produccionMaximo->minimo))?'':'<strong>Max. '.$produccionMaximo->tope.'pt.</strong>';
            $step = (($produccionMaximo->maximo!=0)&&($produccionMaximo->minimo==$produccionMaximo->maximo))?((($produccionMaximo->minimo==$produccionMaximo->tope)&&($produccionMaximo->minimo==$produccionMaximo->maximo))?'1':'0.01'):'0.01';
            $input = (($produccionMaximo->minimo==$produccionMaximo->tope)&&($produccionMaximo->minimo==$produccionMaximo->maximo))?'<td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;"><input type="checkbox" size="5" name="puntajeproduccion'.$loop->index.'" id="puntajeproduccion'.$loop->index.'" value="'.$produccionMaximo->maximo.'"'.$checkedUnidad.' DISABLED onclick="sumar_total();" DISABLED></td><td style="background-color: #eee;color:#333; width:80px; text-align: right; vertical-align:middle;"><span id="spanpuntajeproduccion'.$loop->index.'" ></span><div id="divpuntajeproduccion'.$loop->index.'" class="fValidator-a"></div><input name="valorproduccion'.$loop->index.'" id="valorproduccion'.$loop->index.'" type="hidden" value=""/></td>':(($produccionMaximo->minimo!=$produccionMaximo->maximo)?'<td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;"><input type="number" step="1" min="0" size="5" name="cantproduccion'.$loop->index.'" id="cantproduccion'.$loop->index.'" value="'.$cantidad.'" onblur="validarCampos(' . $loop->index . ', \'produccion\'); sumar_total();"></td><td style="background-color: #eee;color:#333;" align="right"><input type="number" step="'.$step.'" min="0" size="5" name="puntajeproduccion'.$loop->index.'" id="puntajeproduccion'.$loop->index.'" value="'.$puntaje.'" onblur="validarCampos(' . $loop->index . ', \'produccion\'); sumar_total();"><div id="divpuntajeproduccion'.$loop->index.'" class="fValidator-a"></div><input name="valorproduccion'.$loop->index.'" id="valorproduccion'.$loop->index.'" type="hidden" value=""/></td>':'<td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;"><input type="number" step="'.$step.'" min="0" size="5" name="puntajeproduccion'.$loop->index.'" id="puntajeproduccion'.$loop->index.'" value="'.$puntaje.'" onblur="sumar_total();"></td><td style="background-color: #eee;color:#333; width:80px; text-align: right; vertical-align:middle;"><span id="spanpuntajeproduccion'.$loop->index.'" ></span><div id="divpuntajeproduccion'.$loop->index.'" class="fValidator-a"></div><input name="valorproduccion'.$loop->index.'" id="valorproduccion'.$loop->index.'" type="hidden" value=""/></td>');
            $c_u=' c/u';
		 	$hasta = (($produccionMaximo->maximo!=0)&&($produccionMaximo->minimo==$produccionMaximo->maximo))?((($produccionMaximo->minimo==$produccionMaximo->tope)&&($produccionMaximo->minimo==$produccionMaximo->maximo))?$produccionMaximo->maximo. ' pt.':$produccionMaximo->maximo. $c_u):(($produccionMaximo->minimo!=$produccionMaximo->maximo)?'Hasta '.$produccionMaximo->maximo. ' c/u':'Hasta '.$produccionMaximo->tope);
            $descripcion = str_replace('#puntaje#', '<B>'.$produccionMaximo->maximo.' puntos</B>',$produccionMaximo->produccion_nombre);
        @endphp

        @if($produccionMaximo->evaluacion_subgrupo_id)
            <td style="background-color: #eee;color:#333; width:80px">{!! $tope !!}</td>
        @else
            <td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;">{{$produccionMaximo->padre_nombre}}{{$j}}<br>{!! $tope !!}</td>
        @endif
        <td style="width:450px;background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;width: 60%;">{!! $descripcion !!}</td>
        <td style="width:120px;background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;width: 20%;">
            <input type="hidden"  name="id_produccion{{$loop->index}}" id="id_produccion{{$loop->index}}" value="{{$produccionMaximo->id}}">
            <input type="hidden"  name="maxproduccion{{$loop->index}}" id="maxproduccion{{$loop->index}}" value="{{$produccionMaximo->maximo}}">
            <input type="hidden"  name="topeproduccion{{$loop->index}}" id="topeproduccion{{$loop->index}}" value="{{$produccionMaximo->tope}}">
            <input type="hidden"  name="minproduccion{{$loop->index}}" id="minproduccion{{$loop->index}}" value="{{$produccionMaximo->minimo}}">
            <input type="hidden"  name="produccion_evaluacion_grupo_id{{$loop->index}}" id="produccion_evaluacion_grupo_id{{$loop->index}}" value="{{$produccionMaximo->evaluacion_grupo_id}}">{!! $hasta !!}
        </td>{!! $input !!}
        </tr>






    @endforeach
    @php
        $ultimoProduccionMaximo = $produccionMaximos->last();
        //dd($ultimoAntAcadMaximo);
    @endphp
    @if($ultimoProduccionMaximo->grupo_maximo != 0)
        <tr style="background-color: #eee;color:#333;"">
        <td></td>
        <td colspan="3">
            <div align="right"><strong>Subtotal (max. {{$ultimoProduccionMaximo->grupo_maximo}})</strong></div>
            <input type="hidden"  name="maxgrupoproduccion{{$ultimoProduccionMaximo->evaluacion_grupo_id}}" id="maxgrupoproduccion{{$ultimoProduccionMaximo->evaluacion_grupo_id}}" value="{{$ultimoProduccionMaximo->grupo_maximo}}">
        </td>
        <td style="text-align:right"><strong><span id="spangrupoproduccion{{$ultimoProduccionMaximo->evaluacion_grupo_id}}" ></span></strong><div id="divgrupoproduccion{{$ultimoProduccionMaximo->evaluacion_grupo_id}}" class="fValidator-a"></div></td>
        </tr>
    @endif
    @if($ultimoProduccionMaximo->padre_id)
        <tr style="background-color: #eee;color:#333;"">
        <td colspan="4">
            <div align="right">
                <strong>Subtotal {{$ultimoProduccionMaximo->padre_nombre}} (max. {{$ultimoProduccionMaximo->padre_maximo}})</strong>
            </div>
            <input type="hidden"  name="maxgrupoproduccion" id="maxgrupoproduccion" value="{{$ultimoProduccionMaximo->padre_maximo}}"></td><td style="text-align:right">
            <strong><span id="spangrupoproduccion" ></span></strong><div id="divgrupoproduccion" class="fValidator-a"></div>
        </td>
        </tr>
        @endif




        </tbody></table>
    @php
        $primerAnteriors = $anteriorMaximos->first();
        //dd($anteriorMaximos);
        if (!$subsidioAnterior) {
            $checkedAnterior = ' CHECKED ';
        }
    @endphp

    <table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666;margin-bottom:5px">
        <tbody>
        <input type="hidden" name="cantanteriors" id="cantanteriors" value="{{$anteriorMaximos->count()}}">
        <tr style="border-style: solid; border-width: 1px; border-color: #666">
            @php
                $submax = 0;
                $sub = 0;
                $j = 0;
                $max = 0;
            @endphp
            @foreach($anteriorMaximos as $anteriorMaximo)


                <!-- Verificar si se cambia de grupo para mostrar el subtotal -->
        @if ($submax != $anteriorMaximo->evaluacion_grupo_id)
            @php
                //echo $anteriorMaximos[$loop->index]->grupo_maximo;
            @endphp

            @if ($loop->index != 0 && $anteriorMaximos[$loop->index - 1]->grupo_maximo != 0)
                <tr style="background-color: #eee; color: #333;">
                    <td></td>
                    <td colspan="3">
                        <div align="right">
                            <strong>Subtotal (max. {{ $anteriorMaximos[$loop->index - 1]->grupo_maximo }})</strong>
                        </div>
                        <input type="hidden"
                               name="maxgrupoanterior{{ $anteriorMaximos[$loop->index - 1]->evaluacion_grupo_id }}"
                               id="maxgrupoanterior{{ $anteriorMaximos[$loop->index - 1]->evaluacion_grupo_id }}"
                               value="{{ $anteriorMaximos[$loop->index - 1]->grupo_maximo }}" />
                        <strong>
                            <div id="divgrupoanterior{{ $anteriorMaximos[$loop->index - 1]->evaluacion_grupo_id }}" class="fValidator-a"></div>
                        </strong>
                    </td>
                    <td style="text-align:right"><strong><span id="spangrupoAnterior{{ $anteriorMaximos[$loop->index - 1]->evaluacion_grupo_id }}" ></span></strong><div id="divgrupoAnterior{{ $anteriorMaximos[$loop->index - 1]->evaluacion_grupo_id }}" class="fValidator-a"></div></td>
                </tr>
            @endif

            @php
                $submax = $anteriorMaximo->evaluacion_grupo_id;
            @endphp
        @endif

        <!-- Verificar si hay un nuevo subgrupo -->
        @if ($sub != $anteriorMaximo->evaluacion_subgrupo_id && $anteriorMaximo->evaluacion_subgrupo_id)
            @php
                $j++;
            @endphp
            <tr>
                <td style="background-color: #eee; color: #333; width: 80px; vertical-align: middle;">E{{ $j }}</td>
                <td style="background-color: #eee; color: #333;" colspan="4">
                    <div align="left">
                        <strong>{{ $anteriorMaximo->subgrupo_nombre }}</strong>
                    </div>
                </td>
            </tr>

            @php
                $sub = $anteriorMaximo->evaluacion_subgrupo_id;
            @endphp
        @elseif (!$anteriorMaximo->evaluacion_subgrupo_id)
            @php
                $j++;
            @endphp
        @endif
        @php
            $puntajeAnterior = $evaluacion->puntaje_anteriors->where('joven_evaluacion_planilla_anterior_max_id', $anteriorMaximo->id)->first();
            $puntaje = (($puntajeAnterior)&&($puntajeAnterior->puntaje))?(int)$puntajeAnterior->puntaje:'';
            $tope = (($anteriorMaximo->tope==0)||($anteriorMaximo->tope==$anteriorMaximo->minimo))?'':'<strong>Max. '.$anteriorMaximo->tope.'pt.</strong>';
            $hasta = (($anteriorMaximo->maximo!=0)&&($anteriorMaximo->minimo==$anteriorMaximo->maximo))?((($anteriorMaximo->minimo==$anteriorMaximo->tope)&&($anteriorMaximo->minimo==$anteriorMaximo->maximo))?$anteriorMaximo->maximo. ' pt.':$anteriorMaximo->maximo. ' c/u'):'Hasta '.$anteriorMaximo->tope;
            $step = (($anteriorMaximo->maximo!=0)&&($anteriorMaximo->minimo==$anteriorMaximo->maximo))?'1':'0.01';
            $input = (($anteriorMaximo->minimo==$anteriorMaximo->tope)&&($anteriorMaximo->minimo==$anteriorMaximo->maximo))?'<input type="checkbox" size="5" name="puntajeanterior'.$loop->index.'" id="puntajeanterior'.$loop->index.'" value="'.$anteriorMaximo->maximo.'"'.$checkedAnterior.' DISABLED onclick="sumar_total();" DISABLED>':'<input type="number" step="'.$step.'" min="0" size="5" name="puntajeanterior'.$loop->index.'" id="puntajeanterior'.$loop->index.'" value="'.$puntaje.'" onblur="sumar_total();">';
            $descripcion = str_replace('#puntaje#', '<B>'.$anteriorMaximo->maximo.' puntos</B>',$anteriorMaximo->anterior_nombre);
        @endphp

        @if($anteriorMaximo->evaluacion_subgrupo_id)
            <td style="background-color: #eee;color:#333; width:80px">{!! $tope !!}</td>
        @else
            <td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;">E{{$j}}<br>{!! $tope !!}</td>
        @endif
        <td style="width:450px;background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;width: 60%;">{!! $descripcion !!}</td>
        <td style="width:120px;background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;width: 20%;">
            <input type="hidden"  name="id_anterior{{$loop->index}}" id="id_anterior{{$loop->index}}" value="{{$anteriorMaximo->id}}">
            <input type="hidden"  name="maxanterior{{$loop->index}}" id="maxanterior{{$loop->index}}" value="{{$anteriorMaximo->maximo}}">
            <input type="hidden"  name="topeanterior{{$loop->index}}" id="topeanterior{{$loop->index}}" value="{{$anteriorMaximo->tope}}">
            <input type="hidden"  name="minanterior{{$loop->index}}" id="minanterior{{$loop->index}}" value="{{$anteriorMaximo->minimo}}">
            <input type="hidden"  name="anterior_evaluacion_grupo_id{{$loop->index}}" id="anterior_evaluacion_grupo_id{{$loop->index}}" value="{{$anteriorMaximo->evaluacion_grupo_id}}">{!! $hasta !!}
        </td>
        <td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;">{!! $input !!}</td>
        <td style="background-color: #eee;color:#333; width:80px; text-align: right; vertical-align:middle;">
            <span id="spanpuntajeanterior{{$loop->index}}" ></span>
            <div id="divpuntajeanterior{{$loop->index}}" class="fValidator-a"></div>
            <input name="valoranterior{{$loop->index}}" id="valoranterior{{$loop->index}}" type="hidden" value=""/>
        </td></tr>






        @endforeach





        </tbody></table>
    @php
        $primerJustificacions = $justificacionMaximos->first();
        //dd($justificacionMaximos);
    @endphp
    <table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666 ;margin-bottom:5px">
        <tbody><tr style="border-style: solid; border-width: 1px; border-color: #666">
            <td style="background-color: #eee;color:#333; width:80px">{{$primerJustificacions->grupo_nombre}}</td>
            <td style="background-color: #eee;color:#333;" colspan="5"><div align="center"><strong>JUSTIFICACIÓN TÉCNICA DEL SUBSIDIO SOLICITADO</strong></div></td>
        </tr>

        </tbody></table>
    <table style="width:100%; border-style: solid; border-width: 1px;  border-color: #666;margin-bottom:5px">
        <tbody>
        <input type="hidden" name="cantjustificacions" id="cantjustificacions" value="{{$justificacionMaximos->count()}}">
        <tr style="border-style: solid; border-width: 1px; border-color: #666">
            @php
                $submax = 0;
                $sub = 0;
                $j = 0;
                $max = 0;
            @endphp
            @foreach($justificacionMaximos as $justificacionMaximo)


                <!-- Verificar si se cambia de grupo para mostrar el subtotal -->
        @if ($submax != $justificacionMaximo->evaluacion_grupo_id)
            @php
                //echo $justificacionMaximos[$loop->index]->grupo_maximo;
            @endphp

            @if ($loop->index != 0 && $justificacionMaximos[$loop->index - 1]->grupo_maximo != 0)
                <tr style="background-color: #eee; color: #333;">
                    <td></td>
                    <td colspan="3">
                        <div align="right">
                            <strong>Subtotal (max. {{ $justificacionMaximos[$loop->index - 1]->grupo_maximo }})</strong>
                        </div>
                        <input type="hidden"
                               name="maxgrupojustificacions{{ $justificacionMaximos[$loop->index - 1]->evaluacion_grupo_id }}"
                               id="maxgrupojustificacions{{ $justificacionMaximos[$loop->index - 1]->evaluacion_grupo_id }}"
                               value="{{ $justificacionMaximos[$loop->index - 1]->grupo_maximo }}" />
                        <strong>
                            <div id="divgrupojustificacions{{ $justificacionMaximos[$loop->index - 1]->evaluacion_grupo_id }}" class="fValidator-a"></div>
                        </strong>
                    </td>
                    <td style="text-align:right"><strong><span id="spangrupoJustificacion{{ $justificacionMaximos[$loop->index - 1]->evaluacion_grupo_id }}" ></span></strong><div id="divgrupoJustificacion{{ $justificacionMaximos[$loop->index - 1]->evaluacion_grupo_id }}" class="fValidator-a"></div></td>
                </tr>
            @endif

            @php
                $submax = $justificacionMaximo->evaluacion_grupo_id;
            @endphp
        @endif

        <!-- Verificar si hay un nuevo subgrupo -->
        @if ($sub != $justificacionMaximo->evaluacion_subgrupo_id && $justificacionMaximo->evaluacion_subgrupo_id)
            @php
                $j++;
            @endphp
            <tr>
                <td style="background-color: #eee; color: #333; width: 80px; vertical-align: middle;">{{$justificacionMaximo->grupo_nombre}}{{ $j }}</td>
                <td style="background-color: #eee; color: #333;" colspan="4">
                    <div align="left">
                        <strong>{{ $justificacionMaximo->subgrupo_nombre }}</strong>
                    </div>
                </td>
            </tr>

            @php
                $sub = $justificacionMaximo->evaluacion_subgrupo_id;
            @endphp
        @elseif (!$justificacionMaximo->evaluacion_subgrupo_id)
            @php
                $j++;
            @endphp
        @endif
        @php
            $puntajeJustificacion = $evaluacion->puntaje_justificacions->where('joven_evaluacion_planilla_justificacion_max_id', $justificacionMaximo->id)->first();
            $puntaje = (($puntajeJustificacion)&&($puntajeJustificacion->puntaje))?(int)$puntajeJustificacion->puntaje:'';
            $tope = (($justificacionMaximo->tope==0)||($justificacionMaximo->tope==$justificacionMaximo->minimo))?'':'<strong>Max. '.$justificacionMaximo->tope.'pt.</strong>';
            $hasta = (($justificacionMaximo->maximo!=0)&&($justificacionMaximo->minimo==$justificacionMaximo->maximo))?((($justificacionMaximo->minimo==$justificacionMaximo->tope)&&($justificacionMaximo->minimo==$justificacionMaximo->maximo))?$justificacionMaximo->maximo. ' pt.':$justificacionMaximo->maximo. ' c/u'):'Hasta '.$justificacionMaximo->tope;
            $step = (($justificacionMaximo->maximo!=0)&&($justificacionMaximo->minimo==$justificacionMaximo->maximo))?'1':'0.01';
            $input = (($justificacionMaximo->minimo==$justificacionMaximo->tope)&&($justificacionMaximo->minimo==$justificacionMaximo->maximo))?'<input type="checkbox" size="5" name="puntajejustificacions'.$loop->index.'" id="puntajejustificacions'.$loop->index.'" value="'.$justificacionMaximo->maximo.'"'.$checked.' onclick="sumar_total();" DISABLED>':'<input type="number" step="'.$step.'" min="0" size="5" name="puntajejustificacions'.$loop->index.'" id="puntajejustificacions'.$loop->index.'" value="'.$puntaje.'" onblur="sumar_total();">';
            $descripcion = str_replace('#puntaje#', '<B>'.$justificacionMaximo->maximo.' puntos</B>',$justificacionMaximo->justificacion_nombre);
        @endphp

        @if($justificacionMaximo->evaluacion_subgrupo_id)
            <td style="background-color: #eee;color:#333; width:80px">{!! $tope !!}</td>
        @else
            <td style="background-color: #eee;color:#333; width:80px; vertical-align:middle;">{{$justificacionMaximo->grupo_nombre}}{{$j}}<br>{!! $tope !!}</td>
        @endif
        <td style="width:450px;background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;width: 60%;">{!! $descripcion !!}</td>
        <td style="width:120px;background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;width: 20%;">
            <input type="hidden"  name="id_justificacions{{$loop->index}}" id="id_justificacions{{$loop->index}}" value="{{$justificacionMaximo->id}}">
            <input type="hidden"  name="maxjustificacions{{$loop->index}}" id="maxjustificacions{{$loop->index}}" value="{{$justificacionMaximo->maximo}}">
            <input type="hidden"  name="topejustificacions{{$loop->index}}" id="topejustificacions{{$loop->index}}" value="{{$justificacionMaximo->tope}}">
            <input type="hidden"  name="minjustificacions{{$loop->index}}" id="minjustificacions{{$loop->index}}" value="{{$justificacionMaximo->minimo}}">
            <input type="hidden"  name="justificacion_evaluacion_grupo_id{{$loop->index}}" id="justificacion_evaluacion_grupo_id{{$loop->index}}" value="{{$justificacionMaximo->evaluacion_grupo_id}}">{!! $hasta !!}
        </td>
        <td style="background-color: #fff; border-width: 1px; border-color: #eee;text-align: left;">{!! $input !!}</td>
        <td style="background-color: #eee;color:#333; width:80px; text-align: right; vertical-align:middle;">
            <span id="spanpuntajejustificacions{{$loop->index}}" ></span>
            <div id="divpuntajejustificacions{{$loop->index}}" class="fValidator-a"></div>
            <input name="valorjustificacions{{$loop->index}}" id="valorjustificacions{{$loop->index}}" type="hidden" value=""/>
        </td>
        </tr>






        @endforeach
        @php
            $ultimoJustificacionMaximo = $justificacionMaximos->last();
            //dd($ultimoAntAcadMaximo);
        @endphp
        @if($ultimoJustificacionMaximo->grupo_maximo != 0)
            <tr style="background-color: #eee;color:#333;"">
            <td></td>
            <td colspan="3">
                <div align="right"><strong>Subtotal (max. {{$ultimoJustificacionMaximo->grupo_maximo}})</strong></div>
                <input type="hidden"  name="maxgrupojustificacions{{$ultimoJustificacionMaximo->evaluacion_grupo_id}}" id="maxgrupojustificacions{{$ultimoJustificacionMaximo->evaluacion_grupo_id}}" value="{{$ultimoJustificacionMaximo->grupo_maximo}}">
            </td>
            <td style="text-align:right"><strong><span id="spangrupojustificacions{{$ultimoJustificacionMaximo->evaluacion_grupo_id}}" ></span></strong><div id="divgrupojustificacions{{$ultimoJustificacionMaximo->evaluacion_grupo_id}}" class="fValidator-a"></div></td>
            </tr>
        @endif
        @if($ultimoJustificacionMaximo->padre_id)
            <tr style="background-color: #eee;color:#333;"">
            <td colspan="4">
                <div align="right">
                    <strong>Subtotal {{$ultimoJustificacionMaximo->grupo_nombre}} (max. {{$ultimoJustificacionMaximo->padre_maximo}})</strong>
                </div>
                <input type="hidden"  name="maxgrupojustificacions" id="maxgrupojustificacions" value="{{$ultimoJustificacionMaximo->padre_maximo}}"></td><td style="text-align:right">
                <strong><span id="spangrupojustificacions" ></span></strong><div id="divgrupojustificacions" class="fValidator-a"></div>
            </td>
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
                                        <a href="{{ route('jovens.index') }}" class="btn btn-warning">Volver</a>

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



            $( 'input[name="joven_planilla_posgrado_max_id"]:radio' ).change(function() {
                if(this.id=='joven_planilla_posgrado_max_id0'){
                    $('#posgrado').prop('checked', true);

                }
                else{
                    $('#posgrado').prop('checked', false);
                }
            });

        });

        function sumar_total(){
            //$("#{form_id}_input_submit_ajax").show();
            var posgrado = $("input[name='joven_planilla_posgrado_max_id']:checked").val();


            if (posgrado != null){
                var posgradoArray = posgrado.split('-');
            }
            else{
                var posgradoArray = [];
                posgradoArray[1]=0;
            }

            $('#spanPosgrado').text(formatDec(posgradoArray[1],2));

            var total = 0;
            var posgrado = $('#posgrado');
            var factor = $('#factor');
            var spanF = $('#spanF');
            var totalAntAcad=0;
            var submax=0;

            var subpuntaje=parseFloat(total);

            for (var i=0; i<$('#cantantacad').val(); i++) {

                var tope = $('#topeantacad'+i);
                var puntajeElem = $('#puntajeantacad'+i); // Guarda el objeto jQuery en puntajeElem
                var puntaje = 0; // Guarda el valor numérico del puntaje

                var ant_acad_evaluacion_grupo_id = $('#ant_acad_evaluacion_grupo_id'+i);
                var max = $('#maxantacad'+i);
                var min = $('#minantacad'+i);
                var div = $('#divpuntajeantacad'+i);
                div.text('');
                var span = $('#spanpuntajeantacad'+i);
                var valor = $('#valorantacad'+i);

                if (ant_acad_evaluacion_grupo_id != null) {
                    var maxpuntajegrupo = $('#maxgrupoantacad'+ant_acad_evaluacion_grupo_id.val());
                }

                if (puntajeElem.length > 0) {  // Asegúrate de que el elemento existe

                    if (puntajeElem.attr('type') === "checkbox") {
                        puntaje = (puntajeElem.is(":checked")) ? parseFloat(puntajeElem.val()) : 0;
                    }
                    else if (puntajeElem.attr('type') === "hidden") {
                        if ($('#posgrado').is(":checked")) {
                            puntajeElem.val(1);
                        } else {
                            puntajeElem.val(factor.val());
                        }
                        $('#spanF').text(puntajeElem.val());
                        puntaje = formatDec(puntajeElem.val() * (
                            parseFloat($('#spanPosgrado').text()) +
                            parseFloat($('#valorantacad'+(i-1)).val()) +
                            parseFloat($('#valorantacad'+(i-2)).val())
                        ), 2);
                    } else {
                        puntaje = (puntajeElem.val() !== '') ? parseFloat(puntajeElem.val()) : 0;
                    }

                    // Lógica para manejar el puntaje y otros cálculos
                    if ((tope.val() != 0) && (puntajeElem.attr('type') !== "checkbox") &&
                        (((max.val() != 0) && (puntaje * max.val() > parseFloat(tope.val()))) ||
                            ((max.val() == 0) && (puntaje > parseFloat(tope.val()))))) {

                        div.text('Excedido, toma el max.');
                        span.text(formatDec(tope.val(),2));
                        ptotal = tope.val();
                        valor.val(parseFloat(ptotal));
                    } else {
                        if ((puntajeElem.attr('type') === "checkbox") ||
                            (puntajeElem.attr('type') === "hidden") ||
                            (max.val() == 0)) {
                            span.text(formatDec(puntaje, 2));
                            ptotal = puntaje;

                            if (puntajeElem.attr('type') === "hidden") {

                                /*ptotal = puntaje - parseFloat($('#valorantacad'+(i-1)).val()) -
                                    parseFloat($('#valorantacad'+(i-2)).val()) -
                                    parseFloat($('#posgrado').val());*/
                                ptotal = puntaje - parseFloat($('#valorantacad'+(i-1)).val()) -
                                    parseFloat($('#valorantacad'+(i-2)).val());
                            }
                        } else {
                            span.text(formatDec(puntaje * max.val(),2));
                            ptotal = formatDec(puntaje * max.val(), 2);
                            valor.val(parseFloat(ptotal));
                        }
                        div.text('');
                    }

                    if ((i==0)||(submax==ant_acad_evaluacion_grupo_id.val() )){

                        subpuntaje = parseFloat(subpuntaje) + parseFloat(ptotal);
                    }
                    else {
                        var ant_acad_evaluacion_grupo_id = $('#ant_acad_evaluacion_grupo_id'+(i-1));
                        if (ant_acad_evaluacion_grupo_id != null){
                            var maxpuntajegrupoant = $('#maxgrupoantacad'+ant_acad_evaluacion_grupo_id.val());
                            var divgrupo = $('#divgrupoAntacad'+ant_acad_evaluacion_grupo_id.val());
                            var spangrupo = $('#spangrupoAntacad'+ant_acad_evaluacion_grupo_id.val());
                        }
                        if(subpuntaje > maxpuntajegrupoant.val()){
                            totalAntAcad=parseFloat(totalAntAcad)+parseFloat(maxpuntajegrupoant.val());
                            spangrupo.text(parseFloat(maxpuntajegrupoant.val()));
                            divgrupo.text('Excedido, toma el max.');
                        }
                        else{
                            totalAntAcad=parseFloat(totalAntAcad)+parseFloat(subpuntaje);
                            spangrupo.text(formatDec(parseFloat(subpuntaje),2));
                            divgrupo.text('');
                        }
                        subpuntaje=puntaje;
                    }
                    submax=ant_acad_evaluacion_grupo_id.val();

                }
            }

            var ant_acad_evaluacion_grupo_id = $('#ant_acad_evaluacion_grupo_id'+(i-1));

            if (ant_acad_evaluacion_grupo_id != null){

                var maxpuntajegrupoant = $('#maxgrupoantacad'+ant_acad_evaluacion_grupo_id.val());
                var divgrupo = $('#divgrupoAntacad'+ant_acad_evaluacion_grupo_id.val());
                var spangrupo = $('#spangrupoAntacad'+ant_acad_evaluacion_grupo_id.val());
            }

            if(subpuntaje > maxpuntajegrupoant.val()){
                totalAntAcad=parseFloat(totalAntAcad)+parseFloat(maxpuntajegrupoant.val());
                spangrupo.text(parseFloat(maxpuntajegrupoant.val()));
                divgrupo.text('Excedido, toma el max.');
            }
            else{
                totalAntAcad=parseFloat(totalAntAcad)+parseFloat(subpuntaje);
                spangrupo.text(formatDec(parseFloat(subpuntaje),2));
                divgrupo.text('');
            }
            total = parseFloat(total) + parseFloat(totalAntAcad);

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

            var cantidad = $('#cantotros');
            var totalOtros=0;
            var submax=0;
            var subpuntaje=0;
            //control = $('#control');
            for (var i=0; i<cantidad.val(); i++){

                //cant = $('#cantantacad'+i);
                tope = $('#topeotros'+i);

                puntajeElem = $('#puntajeotros'+i);
                otro_evaluacion_grupo_id = $('#otro_evaluacion_grupo_id'+i);
                max = $('#maxotros'+i);
                min = $('#minotros'+i);
                div = $('#divpuntajeotros'+i);
                div.text('');
                span = $('#spanpuntajeotros'+i);
                valor=$('#valorotros'+i);
                if (otro_evaluacion_grupo_id != null){
                    maxpuntajegrupo = $('#maxgrupootros'+otro_evaluacion_grupo_id.val());
                }
                if (puntajeElem != null){

                    if(puntajeElem.attr('type') == "checkbox"){
                        puntaje = (puntajeElem.attr("checked"))?parseFloat(puntajeElem.val()):0;

                    }
                    else puntaje = (puntajeElem.val()!='')?parseFloat(puntajeElem.val()):0;


                    if((tope.val() != 0)&&(puntajeElem.attr('type') != "checkbox")&&(( (max.val()!=0)&&(puntaje*max.val()>parseFloat(tope.val())))||( (max.val()==0)&&(puntaje>parseFloat(tope.val()))))  ){
                        div.text('Excedido, toma el max.');
                        span.text(tope.val());
                        ptotal = tope.val();

                    }
                    else{
                        if((puntajeElem.attr('type') == "checkbox")||(max.val() == 0)){
                            span.text(formatDec(puntaje,2));
                            ptotal = puntaje;
                        }
                        else{
                            span.text(formatDec(puntaje*max.val(),2));
                            ptotal = puntaje*max.val();
                            valor.val(parseFloat(ptotal));

                        }

                        div.text('');

                    }
                    if ((i==0)||(submax==otro_evaluacion_grupo_id.val() )){
                        subpuntaje = parseFloat(subpuntaje) + parseFloat(ptotal);
                    }
                    else {
                        otro_evaluacion_grupo_idant = $('#otro_evaluacion_grupo_id'+(i-1));

                        if (otro_evaluacion_grupo_idant != null){
                            maxpuntajegrupoant = $('#maxgrupootros'+otro_evaluacion_grupo_idant.val());
                            divgrupo = $('#divgrupootros'+otro_evaluacion_grupo_idant.val());
                            spangrupo = $('#spangrupootros'+otro_evaluacion_grupo_idant.val());
                        }

                        if(subpuntaje > maxpuntajegrupoant.val()){
                            totalOtros=parseFloat(totalOtros)+parseFloat(maxpuntajegrupoant.val());
                            spangrupo.text(parseFloat(maxpuntajegrupoant.val()));
                            divgrupo.text('Excedido, toma el max.');
                        }
                        else{
                            totalOtros=parseFloat(totalOtros)+parseFloat(subpuntaje);
                            spangrupo.text(formatDec(parseFloat(subpuntaje),2));
                            divgrupo.text('');
                        }
                        subpuntaje=ptotal;
                    }
                    submax=otro_evaluacion_grupo_id.val();



                }
                //}
                //}

            }
            otro_evaluacion_grupo_id_ant = $('#otro_evaluacion_grupo_id'+(i-1));

            if (otro_evaluacion_grupo_id_ant != null){

                maxpuntajegrupoant = $('#maxgrupootros'+otro_evaluacion_grupo_id_ant.val());
                divgrupo = $('#divgrupootros'+otro_evaluacion_grupo_id_ant.val());
                spangrupo = $('#spangrupootros'+otro_evaluacion_grupo_id_ant.val());

            }

            if(subpuntaje > maxpuntajegrupoant.val()){

                totalOtros=parseFloat(totalOtros)+parseFloat(maxpuntajegrupoant.val());
                spangrupo.text(parseFloat(maxpuntajegrupoant.val()));
                divgrupo.text('Excedido, toma el max.');

            }
            else{

                totalOtros=parseFloat(totalOtros)+parseFloat(subpuntaje);
                spangrupo.text(formatDec(parseFloat(subpuntaje),2));
                divgrupo.text('');
            }
            otro_evaluacion_grupo_id_ant = $('#maxgrupootros');
            if (otro_evaluacion_grupo_id_ant.length > 0){
                maxpuntajegrupoant = $('#maxgrupootros');
                divgrupo = $('#divgrupootros');
                spangrupo = $('#spangrupootros');
            }

            if(totalOtros > maxpuntajegrupoant.val()){
                totalOtros=parseFloat(maxpuntajegrupoant.val());
                spangrupo.text(parseFloat(maxpuntajegrupoant.val()));
                divgrupo.text('Excedido, toma el max.');
            }
            else{

                spangrupo.text(formatDec(parseFloat(totalOtros),2));
                divgrupo.text('');
            }
            total = parseFloat(total) + parseFloat(totalOtros);

            cantidad = $('#cantproduccions');
            var totalProduccion=0;
            var submax=0;
            var subpuntaje=0;


            for (var i=0; i<cantidad.val(); i++){

                cant = $('#cantproduccion'+i);

                tope = $('#topeproduccion'+i);
                puntajeElem = $('#puntajeproduccion'+i);
                produccion_evaluacion_grupo_id = $('#produccion_evaluacion_grupo_id'+i);
                max = $('#maxproduccion'+i);

                min = $('#minproduccion'+i);
                div = $('#divpuntajeproduccion'+i);
                div.text('');
                span = $('#spanpuntajeproduccion'+i);
                valor=$('#valorproduccion'+i);

                if (produccion_evaluacion_grupo_id.length > 0){
                    maxpuntajegrupo = $('#maxgrupoproduccion'+produccion_evaluacion_grupo_id.val());
                }
                if (cant.length > 0){
                    cantidadProduccion = (cant.val()!='')?parseFloat(cant.val()):0;

                    if(puntajeElem.attr('type') == "checkbox"){
                        puntaje = (puntajeElem.prop("checked"))?parseFloat(puntajeElem.val()):0;
                        //console.log(puntaje);

                    }
                    else {
                        puntaje = (puntajeElem.val() != '') ? parseFloat(puntajeElem.val()) : 0;

                    }
                    if((cantidadProduccion!=0)&&(puntaje==0)){
                        /*div.text('Falta el puntaje');*/


                    }
                    else{


                        /*if(puntaje>(cantidadProduccion*parseFloat(max.val()))){*/

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
                        puntaje = (puntajeElem.prop("checked"))?parseFloat(puntajeElem.val()):0;

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
                    //alert('i: '+i+' - submax: '+submax+' - produccion_evaluacion_grupo_id.val(): '+produccion_evaluacion_grupo_id.val());
                    if ((i==0)||(submax==produccion_evaluacion_grupo_id.val() )){

                        subpuntaje = parseFloat(subpuntaje) + parseFloat(ptotal);
                        //alert(subpuntaje+' > '+maxpuntajegrupoant.val()+' > '+totalProduccion+' > '+ptotal)

                    }
                    else {

                        produccion_evaluacion_grupo_id_ant = $('#produccion_evaluacion_grupo_id'+(i-1));

                        if (produccion_evaluacion_grupo_id_ant.length > 0){
                            maxpuntajegrupoant = $('#maxgrupoproduccion'+produccion_evaluacion_grupo_id_ant.val());
                            divgrupo = $('#divgrupoProduccion'+produccion_evaluacion_grupo_id_ant.val());
                            spangrupo = $('#spangrupoProduccion'+produccion_evaluacion_grupo_id_ant.val());
                        }

                        if(subpuntaje > maxpuntajegrupoant.val()){
                            totalProduccion=parseFloat(totalProduccion)+parseFloat(maxpuntajegrupoant.val());
                            spangrupo.text(parseFloat(maxpuntajegrupoant.val()));
                            divgrupo.text('Excedido, toma el max.');

                        }
                        else{
                            totalProduccion=parseFloat(totalProduccion)+parseFloat(subpuntaje);
                            spangrupo.text(formatDec(parseFloat(subpuntaje),2));
                            divgrupo.text('');

                        }

                        subpuntaje=ptotal;
                    }

                    submax=produccion_evaluacion_grupo_id.val();



                }
                //}
                //}

            }
            produccion_evaluacion_grupo_id_ant = $('#produccion_evaluacion_grupo_id'+(i-1));
            if (produccion_evaluacion_grupo_id_ant.length > 0){
                maxpuntajegrupoant = $('#maxgrupoproduccion'+produccion_evaluacion_grupo_id_ant.val());
                divgrupo = $('#divgrupoProduccion'+produccion_evaluacion_grupo_id_ant.val());
                spangrupo = $('#spangrupoProduccion'+produccion_evaluacion_grupo_id_ant.val());
            }
            if(subpuntaje > maxpuntajegrupoant.val()){
                totalProduccion=parseFloat(totalProduccion)+parseFloat(maxpuntajegrupoant.val());
                spangrupo.text(parseFloat(maxpuntajegrupoant.val()));
                divgrupo.text('Excedido, toma el max.');
            }
            else{
                totalProduccion=parseFloat(totalProduccion)+parseFloat(subpuntaje);
                spangrupo.text(formatDec(parseFloat(subpuntaje),2));
                divgrupo.text('');
            }


            produccion_evaluacion_grupo_id_ant = $('#maxgrupoproduccion');
            if (produccion_evaluacion_grupo_id_ant.length > 0){
                maxpuntajegrupoant = $('#maxgrupoproduccion');
                divgrupo = $('#divgrupoproduccion');
                spangrupo = $('#spangrupoproduccion');
            }
            //alert(subpuntaje+' > '+maxpuntajegrupoant.val()+' > '+totalProduccion)
            if(totalProduccion > maxpuntajegrupoant.val()){
                totalProduccion=parseFloat(maxpuntajegrupoant.val());
                spangrupo.text(parseFloat(maxpuntajegrupoant.val()));
                divgrupo.text('Excedido, toma el max.');
            }
            else{
                //totalProduccion=totalProduccion+parseFloat(subpuntaje);
                spangrupo.text(formatDec(parseFloat(totalProduccion),2));
                divgrupo.text('');
            }

            total = parseFloat(total) + parseFloat(totalProduccion);

            cantidad = $('#cantanteriors');
            var totalAnterior=0;
            var submax=0;
            var subpuntaje=0;
            //control = $('#control');
            for (var i=0; i<cantidad.val(); i++){

                //nu_cant = $('#nu_cantantacad'+i);
                tope = $('#topeanterior'+i);
                puntajeElem = $('#puntajeanterior'+i);
                anterior_evaluacion_grupo_id = $('#anterior_evaluacion_grupo_id'+i);
                max = $('#maxanterior'+i);
                min = $('#minanterior'+i);
                div = $('#divpuntajeanterior'+i);
                div.text('');
                span = $('#spanpuntajeanterior'+i);
                valor=$('#valoranterior'+i);
                if (anterior_evaluacion_grupo_id != null){
                    maxpuntajegrupo = $('#maxgrupoanterior'+anterior_evaluacion_grupo_id.val());
                }
                if (puntajeElem != null){

                    if(puntajeElem.attr('type') == "checkbox"){
                        puntaje = (puntajeElem.prop("checked"))?parseFloat(puntajeElem.val()):0;

                    }
                    else puntaje = (puntajeElem.val()!='')?parseFloat(puntajeElem.val()):0;


                    if((tope.val() != 0)&&(puntajeElem.attr('type') != "checkbox")&&(( (max.val()!=0)&&(puntaje*max.val()>parseFloat(tope.val())))||( (max.val()==0)&&(puntaje>parseFloat(tope.val()))))  ){
                        div.text('Excedido, toma el max.');
                        span.text(tope.val());
                        ptotal = tope.val();

                    }
                    else{
                        if((puntajeElem.attr('type') == "checkbox")||(max.val() == 0)){
                            span.text(formatDec(puntaje,2));
                            ptotal = puntaje;
                        }
                        else{
                            span.text(formatDec(puntaje*max.val(),2));
                            ptotal = puntaje*max.val();
                            valor.val(parseFloat(ptotal));

                        }

                        div.text('');

                    }
                    if ((i==0)||(submax==anterior_evaluacion_grupo_id.val() )){
                        subpuntaje = parseFloat(subpuntaje) + parseFloat(ptotal);
                    }
                    else {
                        anterior_evaluacion_grupo_id_ant = $('#anterior_evaluacion_grupo_id'+(i-1));
                        if (anterior_evaluacion_grupo_id_ant != null){
                            maxpuntajegrupoant = $('#maxgrupo'+anterior_evaluacion_grupo_id_ant.val());
                            divgrupo = $('#divgrupoAnterior'+anterior_evaluacion_grupo_id_ant.val());
                            spangrupo = $('#spangrupoAnterior'+anterior_evaluacion_grupo_id_ant.val());
                        }
                        if(subpuntaje > maxpuntajegrupoant.val()){
                            totalAnterior=parseFloat(totalAnterior)+parseFloat(maxpuntajegrupoant.val());
                            spangrupo.text(parseFloat(maxpuntajegrupoant.val()));
                            divgrupo.text('Excedido, toma el max.');
                        }
                        else{
                            totalAnterior=parseFloat(totalAnterior)+parseFloat(subpuntaje);
                            spangrupo.text(formatDec(parseFloat(subpuntaje),2));
                            divgrupo.text('');
                        }
                        subpuntaje=puntaje;
                    }
                    submax=anterior_evaluacion_grupo_id.val();



                }
                //}
                //}

            }
            anterior_evaluacion_grupo_id_ant = $('#anterior_evaluacion_grupo_id'+(i-1));
            if (anterior_evaluacion_grupo_id_ant != null){
                maxpuntajegrupoant = $('#maxgrupoanterior'+anterior_evaluacion_grupo_id_ant.val());
                divgrupo = $('#divgrupoAnterior'+anterior_evaluacion_grupo_id_ant.val());
                spangrupo = $('#spangrupoAnterior'+anterior_evaluacion_grupo_id_ant.val());
            }
            if(subpuntaje > maxpuntajegrupoant.val()){
                totalAnterior=parseFloat(totalAnterior)+parseFloat(maxpuntajegrupoant.val());
                spangrupo.text(parseFloat(maxpuntajegrupoant.val()));
                divgrupo.text('Excedido, toma el max.');
            }
            else{
                totalAnterior=parseFloat(totalAnterior)+parseFloat(subpuntaje);
                spangrupo.text(formatDec(parseFloat(subpuntaje),2));
                divgrupo.text('');
            }

            total = parseFloat(total) + parseFloat(totalAnterior);

            var cantidad = $('#cantjustificacions');
            var totalJustificacions=0;
            var submax=0;
            var subpuntaje=0;
            //control = $('#control');
            for (var i=0; i<cantidad.val(); i++){

                //cant = $('#cantantacad'+i);
                tope = $('#topejustificacions'+i);

                puntajeElem = $('#puntajejustificacions'+i);
                justificacion_evaluacion_grupo_id = $('#justificacion_evaluacion_grupo_id'+i);
                max = $('#maxjustificacions'+i);
                min = $('#minjustificacions'+i);
                div = $('#divpuntajejustificacions'+i);
                div.text('');
                span = $('#spanpuntajejustificacions'+i);
                valor=$('#valorjustificacions'+i);
                if (justificacion_evaluacion_grupo_id != null){
                    maxpuntajegrupo = $('#maxgrupojustificacions'+justificacion_evaluacion_grupo_id.val());
                }
                if (puntajeElem != null){

                    if(puntajeElem.attr('type') == "checkbox"){
                        puntaje = (puntajeElem.prop("checked"))?parseFloat(puntajeElem.val()):0;

                    }
                    else puntaje = (puntajeElem.val()!='')?parseFloat(puntajeElem.val()):0;


                    if((tope.val() != 0)&&(puntajeElem.attr('type') != "checkbox")&&(( (max.val()!=0)&&(puntaje*max.val()>parseFloat(tope.val())))||( (max.val()==0)&&(puntaje>parseFloat(tope.val()))))  ){
                        div.text('Excedido, toma el max.');
                        span.text(tope.val());
                        ptotal = tope.val();

                    }
                    else{
                        if((puntajeElem.attr('type') == "checkbox")||(max.val() == 0)){
                            span.text(formatDec(puntaje,2));
                            ptotal = puntaje;
                        }
                        else{
                            span.text(formatDec(puntaje*max.val(),2));
                            ptotal = puntaje*max.val();
                            valor.val(parseFloat(ptotal));

                        }

                        div.text('');

                    }
                    if ((i==0)||(submax==justificacion_evaluacion_grupo_id.val() )){
                        subpuntaje = parseFloat(subpuntaje) + parseFloat(ptotal);
                    }
                    else {
                        justificacion_evaluacion_grupo_idant = $('#justificacion_evaluacion_grupo_id'+(i-1));

                        if (justificacion_evaluacion_grupo_idant != null){
                            maxpuntajegrupoant = $('#maxgrupojustificacions'+justificacion_evaluacion_grupo_idant.val());
                            divgrupo = $('#divgrupojustificacions'+justificacion_evaluacion_grupo_idant.val());
                            spangrupo = $('#spangrupojustificacions'+justificacion_evaluacion_grupo_idant.val());
                        }

                        if(subpuntaje > maxpuntajegrupoant.val()){
                            totalJustificacions=parseFloat(totalJustificacions)+parseFloat(maxpuntajegrupoant.val());
                            spangrupo.text(parseFloat(maxpuntajegrupoant.val()));
                            divgrupo.text('Excedido, toma el max.');
                        }
                        else{
                            totalJustificacions=parseFloat(totalJustificacions)+parseFloat(subpuntaje);
                            spangrupo.text(formatDec(parseFloat(subpuntaje),2));
                            divgrupo.text('');
                        }
                        subpuntaje=ptotal;
                    }
                    submax=justificacion_evaluacion_grupo_id.val();



                }
                //}
                //}

            }
            justificacion_evaluacion_grupo_id_ant = $('#justificacion_evaluacion_grupo_id'+(i-1));

            if (justificacion_evaluacion_grupo_id_ant != null){

                maxpuntajegrupoant = $('#maxgrupojustificacions'+justificacion_evaluacion_grupo_id_ant.val());
                divgrupo = $('#divgrupojustificacions'+justificacion_evaluacion_grupo_id_ant.val());
                spangrupo = $('#spangrupojustificacions'+justificacion_evaluacion_grupo_id_ant.val());

            }

            if(subpuntaje > maxpuntajegrupoant.val()){

                totalJustificacions=parseFloat(totalJustificacions)+parseFloat(maxpuntajegrupoant.val());
                spangrupo.text(parseFloat(maxpuntajegrupoant.val()));
                divgrupo.text('Excedido, toma el max.');

            }
            else{

                totalJustificacions=parseFloat(totalJustificacions)+parseFloat(subpuntaje);
                spangrupo.text(formatDec(parseFloat(subpuntaje),2));
                divgrupo.text('');
            }
            justificacion_evaluacion_grupo_id_ant = $('#maxgrupojustificacions');
            if (justificacion_evaluacion_grupo_id_ant.length > 0){
                maxpuntajegrupoant = $('#maxgrupojustificacions');
                divgrupo = $('#divgrupojustificacions');
                spangrupo = $('#spangrupojustificacions');
            }

            if(totalJustificacions > maxpuntajegrupoant.val()){
                totalJustificacions=parseFloat(maxpuntajegrupoant.val());
                spangrupo.text(parseFloat(maxpuntajegrupoant.val()));
                divgrupo.text('Excedido, toma el max.');
            }
            else{

                spangrupo.text(formatDec(parseFloat(totalJustificacions),2));
                divgrupo.text('');
            }
            total = parseFloat(total) + parseFloat(totalJustificacions);



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
