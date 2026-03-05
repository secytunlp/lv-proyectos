
<?php $__env->startSection('headSection'); ?>

    <!-- AdminLTE Skins. Choose a skin from the css/skins
           folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?php echo e(asset('dist/css/skins/_all-skins.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('bower_components/select2/dist/css/select2.min.css')); ?>">


<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <i class="fa fa-plane" aria-hidden="true"></i>Viajes/Estadías
                <small>Editar</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo e(route('home')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="<?php echo e(route('viajes.index')); ?>">Viajes/Estadías</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Crear</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="<?php echo e(route('viajes.update',$viaje->id)); ?> }}" method="post" enctype="multipart/form-data" onsubmit="return prepararEnvio();" novalidate>
                            <?php echo e(csrf_field()); ?>

                            <?php echo e(method_field('PUT')); ?>


                            <div class="box-body">
                                <?php echo $__env->make('includes.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#datos_personales" aria-controls="datos_personales" role="tab" data-toggle="tab">Datos Personales</a></li>
                                    <li role="presentation"><a href="#universidad" aria-controls="universidad" role="tab" data-toggle="tab">Universidad</a></li>
                                    <li role="presentation"><a href="#investigacion" aria-controls="investigacion" role="tab" data-toggle="tab">Investigación</a></li>
                                    <li role="presentation"><a href="#categorizacion" aria-controls="categorizacion" role="tab" data-toggle="tab">Categorización</a></li>
                                    <li role="presentation"><a href="#becario" aria-controls="becario" role="tab" data-toggle="tab">Becas</a></li>
                                    <li role="presentation"><a href="#proyectos" aria-controls="proyectos" role="tab" data-toggle="tab">Proyectos</a></li>
                                    <li role="presentation"><a href="#tipo" aria-controls="tipo" role="tab" data-toggle="tab">Tipo</a></li>
                                    <li role="presentation"><a href="#descripcion" aria-controls="descripcion" role="tab" data-toggle="tab">Motivo</a></li>
                                    <li role="presentation"><a href="#lugar" aria-controls="lugar" role="tab" data-toggle="tab">Lugar</a></li>
                                    <li role="presentation"><a href="#montos" aria-controls="montos" role="tab" data-toggle="tab">Montos</a></li>
                                    <li role="presentation"><a href="#presupuesto" aria-controls="presupuesto" role="tab" data-toggle="tab">Presupuesto</a></li>

                                    <!-- Agrega más pestañas según sea necesario -->
                                </ul>
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="datos_personales">

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('apellido', 'Apellido')); ?>

                                                    <?php echo e(Form::text('apellido', $viaje->investigador->persona->apellido, ['class' => 'form-control','placeholder'=>'Apellido','disabled'])); ?>

                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('nombre', 'Nombre')); ?>

                                                    <?php echo e(Form::text('nombre', $viaje->investigador->persona->nombre, ['class' => 'form-control','placeholder'=>'Nombre','disabled'])); ?>

                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('cuil', 'CUIL')); ?>

                                                    <?php echo e(Form::text('cuil', $viaje->investigador->persona->cuil, ['class' => 'form-control','placeholder'=>'XX-XXXXXXXX-X','disabled'])); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('email', 'Email')); ?>

                                                    <?php echo e(Form::email('email', $viaje->email, ['class' => 'form-control','placeholder'=>'Email'])); ?>

                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('telefono', 'Teléfono')); ?>

                                                    <?php echo e(Form::text('telefono', $viaje->telefono, ['class' => 'form-control','placeholder'=>'Teléfono'])); ?>

                                                </div>
                                            </div>


                                        </div>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('notificacion', 'Acepto recibir toda notificación relativa a la presente solicitud
en la dirección de correo electrónico declarada precedentemente')); ?>

                                                    <?php echo e(Form::checkbox('notificacion', 1,($viaje->notificacion)?true:false)); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('calle', 'Calle')); ?>

                                                    <?php echo e(Form::text('calle', $viaje->calle, ['class' => 'form-control','placeholder'=>'Calle'])); ?>

                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('nro', 'Número')); ?>

                                                    <?php echo e(Form::text('nro', $viaje->nro, ['class' => 'form-control','placeholder'=>'Número'])); ?>


                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('piso', 'Piso')); ?>

                                                    <?php echo e(Form::text('piso', $viaje->piso, ['class' => 'form-control','placeholder'=>'Piso'])); ?>

                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('depto', 'Departamento')); ?>

                                                    <?php echo e(Form::text('depto', $viaje->depto, ['class' => 'form-control','placeholder'=>'Departamento'])); ?>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('cp', 'Código Postal')); ?>

                                                    <?php echo e(Form::text('cp', $viaje->cp, ['class' => 'form-control','placeholder'=>'Código Postal'])); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group">

                                                    <label for="actividades" style="color: red">Para inscribirse y participar en esta convocatoria debe tener creado su Perfil de google scholar (google académico)</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('scholar', 'Link del perfil google scholar (google académico) generado con el correo institucional de la U.N.L.P.')); ?>

                                                    <?php echo e(Form::text('scholar', $viaje->scholar, ['class' => 'form-control','placeholder'=>'https://scholar.google.com/citations?user=...=es'])); ?>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group">

                                                    <label for="actividades" style="color: red">En <a href="https://unlp.edu.ar/investigacion/google-scholar-9730" target="_blank">https://unlp.edu.ar/investigacion/google-scholar-9730</a> hay un tutorial que explica el sencillo procedimiento para la creación del perfil. Quien no posea una dirección de correo electrónico con extensión unlp.edu.ar debe tramitarla en la dependencia donde se desempeña.</label>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="universidad">
                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Título de Grado</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                    <table class="table" style="width: 50%">
                                                        <thead>

                                                        <th>Título</th>
                                                        <th>Egreso</th>
                                                        <!--<th><a href="#" class="addRow"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                        </thead>

                                                        <tbody id="cuerpoTitulo">
                                                        <tr>

                                                            <td><?php echo e(Form::select('titulos[]',$titulos, ($viaje->titulo_id)?$viaje->titulo_id:'',['class' => 'form-control js-example-basic-single', 'style' => 'width: 400px'])); ?></td>
                                                            <td><?php echo e(Form::date('egresos[]', ($viaje->egresogrado) ? date('Y-m-d', strtotime($viaje->egresogrado)) : '',  ['class' => 'form-control', 'style' => 'width:150px;'])); ?></td>

                                                            <!--<td><a href="#" class="btn btn-danger remove"><i class="glyphicon glyphicon-remove"></i></a></td>-->
                                                        </tr>

                                                        </tbody>




                                                    </table>
                                                </div>
                                            </div>

                                        </fieldset>



                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Cargo Docente</legend>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">

                                                        <label for="actividades" style="color: red">Verifique el cargo docente y dedicación, en caso de estar incorrectos modifíquelos</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                    <table class="table" style="width: 50%">
                                                        <thead>

                                                        <th>Cargo</th>
                                                        <th>Dedicación</th>
                                                        <th>Ingreso</th>
                                                        <th>U. Académica</th>
                                                        <!--<th>Universidad</th>
                                                        <th>Activo</th>
                                                        <th><a href="#" class="addRowCargo"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                        </thead>

                                                        <tbody id="cuerpoCargos">
                                                        <tr>

                                                            <td><?php echo e(Form::select('cargos[]',$cargos, $viaje->cargo_id,['class' => 'form-control', 'style' => 'width: 200px'])); ?></td>
                                                            <td><?php echo e(Form::select('deddocs[]',[''=>'','Exclusiva'=>'Exclusiva','Semi Exclusiva'=>'Semi Exclusiva','Simple'=>'Simple'], $viaje->deddoc,['class' => 'form-control', 'style' => 'width: 120px'])); ?></td>
                                                            <td><?php echo e(Form::date('ingresos[]', ($viaje->ingreso_cargo) ? date('Y-m-d', strtotime($viaje->ingreso_cargo)) : '', ['class' => 'form-control', 'style' => 'width:150px;'])); ?></td>
                                                            <td><?php echo e(Form::select('facultads[]',$facultades, $viaje->facultad_id,['class' => 'form-control', 'style' => 'width: 300px'])); ?></td>

                                                        </tr>

                                                        </tbody>




                                                    </table>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <div class="row">

                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('unidad', 'Lugar de Trabajo en la UNLP (Unidad de Investigación: Laboratorio, Centro, Instituto, etc.)')); ?>

                                                    <?php echo e(Form::select('unidad_id',  $unidads,$viaje->unidad_id, ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'unidad_id'])); ?>


                                                </div>
                                            </div>


                                        </div>
                                        <div class="row">

                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('disciplina', 'Debe cargar la Disciplina primaria - disciplina secundaria - especialidad (separadas por guiones)
Esta información será tenida en cuenta en el proceso de evaluación')); ?>

                                                    <?php echo e(Form::text('disciplina', $viaje->disciplina, ['class' => 'form-control','placeholder'=>'Disciplina'])); ?>


                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="investigacion">

                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Carrera de Investigación</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                    <table class="table" style="width: 50%">
                                                        <thead>

                                                        <th>Cargo</th>
                                                        <th>Institución</th>
                                                        <th>Ingreso</th>

                                                        <!--<th>Actual</th>
                                                        <th><a href="#" class="addRowCarrerainv"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                        </thead>

                                                        <tbody id="cuerpoCarrerainvs">
                                                        <tr>

                                                            <td><?php echo e(Form::select('carrerainvs[]',$carrerainvs, $viaje->carrerainv_id,['class' => 'form-control', 'style' => 'width: 200px'])); ?></td>
                                                            <td><?php echo e(Form::select('organismos[]',$organismos, $viaje->organismo_id,['class' => 'form-control', 'style' => 'width: 150px'])); ?></td>
                                                            <td><?php echo e(Form::date('carringresos[]', ($viaje->ingreso_carrerainv) ? date('Y-m-d', strtotime($viaje->ingreso_carrerainv)) : '', ['class' => 'form-control', 'style' => 'width:150px;'])); ?></td>


                                                            <!--<td><?php echo e(Form::radio('actual', 1, true,['id' => 'actual_1'])); ?></td>
                                                        <td><a href="#" class="btn btn-danger removeCarrerainv"><i class="glyphicon glyphicon-remove"></i></a></td>-->
                                                        </tr>

                                                        </tbody>




                                                    </table>
                                                </div>
                                            </div>
                                            <div class="row">

                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <?php echo e(Form::label('unidad', 'Lugar de Trabajo')); ?>

                                                        <?php echo e(Form::select('unidadcarrera_id',  $unidads,$viaje->unidadcarrera_id, ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'unidadcarrera_id'])); ?>


                                                    </div>
                                                </div>


                                            </div>
                                        </fieldset>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="categorizacion">

                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Categoría SPU</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                    <table class="table" style="width: 50%">
                                                        <thead>

                                                        <th>Categoría</th>


                                                        </thead>

                                                        <tbody id="cuerpoCategorias">
                                                        <tr>

                                                            <td><?php echo e(Form::select('categorias[]',$categorias, $viaje->categoria_id,['class' => 'form-control', 'style' => 'width: 60px','disabled'])); ?></td>

                                                        </tr>

                                                        </tbody>




                                                    </table>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Categoría SICADI</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                    <table class="table" style="width: 50%">
                                                        <thead>

                                                        <th>Categoría</th>


                                                        </thead>

                                                        <tbody id="cuerpoSicadis">
                                                        <tr>

                                                            <td><?php echo e(Form::select('sicadis[]',$sicadis, $viaje->sicadi_id,['class' => 'form-control', 'style' => 'width: 120px','disabled'])); ?></td>

                                                        </tr>

                                                        </tbody>




                                                    </table>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="becario">

                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Beca actual</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                    <table class="table" style="width: 50%">
                                                        <thead>


                                                        <th>Institución</th>
                                                        <th>Beca</th>
                                                        <th>Período</th>
                                                        <!--<th>Hasta</th>-->
                                                        <th>UNLP</th>
                                                        <!--<th><a href="#" class="addRowBeca"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                        </thead>

                                                        <tbody id="cuerpoBecaActual">
                                                        <tr>

                                                            <td><?php echo e(Form::select('institucion',[''=>'','ANPCyT'=>'ANPCyT','CIC'=>'CIC','CONICET'=>'CONICET','UNLP'=>'UNLP','CIN'=>'CIN','OTRA'=>'OTRA'], $viaje->institucion,['class' => 'form-control institucionActual_select', 'style' => 'width: 150px'])); ?></td>

                                                            <td><?php echo e(Form::select('beca', \App\Helpers\BecaHelper::obtenerOpcionesBecaPorInstitucion($viaje->institucion), $viaje->beca, ['class' => 'form-control becaActual_select', 'style' => 'width: 150px'])); ?>

                                                            </td>
                                                            <td>
                                                                <?php echo e(Form::text('periodobeca',
                                                                                $viaje->periodobeca,
                                                                                ['class' => 'form-control', 'placeholder' => 'Período'])); ?>



                                                            </td>

                                                            <td><?php echo e(Form::checkbox('unlp', 1,($viaje->unlp)?true:false, ['disabled'])); ?></td>

                                                            <!--<td><a href="#" class="btn btn-danger removeCategoria"><i class="glyphicon glyphicon-remove"></i></a></td>-->
                                                        </tr>

                                                        </tbody>




                                                    </table>
                                                </div>
                                            </div>
                                            <div class="row">

                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <?php echo e(Form::label('unidad', 'Lugar de Trabajo')); ?>

                                                        <?php echo e(Form::select('unidadbeca_id',  $unidads,$viaje->unidadbeca_id, ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'unidadbeca_id'])); ?>


                                                    </div>
                                                </div>


                                            </div>
                                        </fieldset>



                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="proyectos">
                                        <div class="row" style="margin: 10px;">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('director', 'Seleccione el proyecto de investigación en el marco del cual realizará la actividad ')); ?>


                                                </div>
                                            </div>
                                        </div>
                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Actuales</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                    <table class="table" style="width: 100%">
                                                        <thead>

                                                        <th>Seleccionado</th>
                                                        <th>Código</th>
                                                        <th>Título</th>
                                                        <th>Director</th>
                                                        <th>Inicio</th>
                                                        <th>Fin</th>
                                                        <th>Estado</th>

                                                        </thead>

                                                        <tbody id="cuerpoProyectoActual">
                                                        <?php $__currentLoopData = $proyectosActuales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proyectoActual): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <tr>
                                                                <td>
                                                                    <?php echo e(Form::radio('proyectoSeleccionado', $proyectoActual['id'], $proyectoActual['seleccionado'], ['class' => 'form-check-input'])); ?>

                                                                </td>
                                                                <td><?php echo e(Form::text('codigoActual[]', $proyectoActual['codigo'], ['class' => 'form-control', 'style' => 'width:150px;','disabled'])); ?></td>
                                                                <td><?php echo e(Form::textarea('tituloActual[]', $proyectoActual['titulo'], ['class' => 'form-control', 'style' => 'width:450px;','disabled','rows'=>2])); ?></td>
                                                                <td><?php echo e(Form::text('directorActual[]', $proyectoActual['director'], ['class' => 'form-control', 'style' => 'width:200px;','disabled'])); ?></td>
                                                                <td><?php echo e(Form::date('inicioActual[]', ($proyectoActual['inicio'])?date('Y-m-d', strtotime($proyectoActual['inicio'])):'', ['class' => 'form-control', 'style' => 'width:120px;','disabled'])); ?></td>
                                                                <td><?php echo e(Form::date('finActual[]', ($proyectoActual['fin'])?date('Y-m-d', strtotime($proyectoActual['fin'])):'', ['class' => 'form-control', 'style' => 'width:120px;','disabled'])); ?></td>
                                                                <td><?php echo e(Form::text('estadoActual[]', $proyectoActual['estado'], ['class' => 'form-control', 'style' => 'width:150px;','disabled'])); ?></td>
                                                            </tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </tbody>




                                                    </table>
                                                </div>
                                            </div>

                                        </fieldset>


                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="tipo">

                                        <div class="row">

                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="actividades" style="color: grey">En el caso de contar con un solo cargo docente debe seleccionar la facultad por donde posee dicho cargo, si posee cargos en facultades distintas debe seleccionar la facultad por donde usted tiene un cargo docente y realiza su tarea de investigación</label>


                                                </div>
                                            </div>


                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('facultadplanilla', 'U. Académica por la que presenta la solicitud')); ?>

                                                    <?php echo e(Form::select('facultadplanilla_id',  $facultades,$viaje->facultadplanilla_id, ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'facultadplanilla_id'])); ?>


                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">

                                                    <?php echo e(Form::label('tipo', 'Tipo')); ?>

                                                    <?php echo e(Form::select('tipo',[''=>'','Investigador Formado'=>'Investigador Formado','Investigador En Formación'=>'Investigador En Formación'], $viaje->tipo,['class' => 'form-control'])); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <ul class="desc">
                                                        <li>Se considerará  postulantes <strong>formados</strong> a los docentes-investigadores con categoría I, II, III o miembro de organismos de CyT con categoría de Adjunto o superior o antecedentes equivalentes </li>
                                                        <li>Se considerará  postulante <strong>en formación</strong> a los docentes-investigadores con categoría IV, V, miembro de organismos de CyT con categoría de Asistente, Becarios, Tesistas o antecedentes equivalentes</li>
                                                    </ul>


                                                </div>
                                            </div>


                                        </div>
                                        <div class="row">

                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="actividades" style="color: grey">Si usted tiene categoría IV o V y es Investigador de un organismo de CyT con categoría de Adjunto o superior debe seleccionar Investigador Formado</label>


                                                </div>
                                            </div>


                                        </div>

                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="descripcion">



                                        <div class="row">

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('motivo', 'Motivo')); ?>

                                                    <?php echo e(Form::select('motivo',  [''=>'Seleccionar...','A) Reuniones Científicas'=>'A) Reuniones Científicas','B) Estadía de trabajo para investigar en ámbitos académicos externos a la UNLP'=>'B) Estadía de trabajo para investigar en ámbitos académicos externos a la UNLP','C) ESTADÍA DE TRABAJO EN LA UNLP PARA UN INVESTIGADOR INVITADO'=>'C) ESTADÍA DE TRABAJO EN LA UNLP PARA UN INVESTIGADOR INVITADO'],$viaje->motivo, ['class' => 'form-control', 'style' => 'width: 100%','id'=>'motivo'])); ?>


                                                </div>
                                            </div>


                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="curriculum">Curriculum</label>
                                                    <input type="file" name="curriculum" class="form-control" placeholder="">
                                                    <?php if(!empty($viaje->curriculum)): ?>
                                                        <a href="<?php echo e(url($viaje->curriculum)); ?>" target="_blank">Descargar Curriculum</a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="actividades" style="color: red">IMPORTANTE: EL CV deberá ser el generado (pdf/doc) por el sistema SIGEVA-UNLP (banco de datos de
                                                        actividades de ciencia y técnica)</label>


                                                </div>
                                            </div>

                                        </div>
                                        <div id="divA" style="display: none">

                                            <div class="row">
                                                <div class="col-md-8">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('objetivo', 'OBJETIVOS DEL VIAJE - JUSTIFICACION Y RELACION CON EL PROYECTO DE INVESTIGACION')); ?>

                                                        <?php echo e(Form::textarea('objetivo', $viaje->objetivo, ['class' => 'form-control'])); ?>


                                                    </div>
                                                </div>
                                            </div>



                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <strong>Relevancia Institucional:
                                                        <ul class="desc">
                                                            <li>La importancia del evento con relación al tema del solicitante </li>
                                                            <li>Su contribución al proyecto que integra </li>
                                                            <li>La relevancia de su participación para transferir y fortalecer líneas de investigación de la Unidad Académica</li>
                                                            <li>El establecimiento o afianzamiento de vínculos con otros equipos o investigadores particulares</li>
                                                            <li>Los potenciales canales de difusión que pueden surgir a partir del evento</li>
                                                        </ul>

                                                        </strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">

                                                    <div class="form-group">

                                                        <?php echo e(Form::textarea('relevanciaA', $viaje->relevanciaA, ['class' => 'form-control', 'rows' => 4])); ?>


                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">

                                                <div class="col-md-4">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('congresonombre', 'Nombre de la Reunión Científica')); ?>

                                                        <?php echo e(Form::text('congresonombre', $viaje->congresonombre, ['class' => 'form-control'])); ?>


                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <?php echo e(Form::label('nacional', 'Carácter')); ?>

                                                        <?php echo e(Form::select('nacional_id',  ['Nacional'=>'Nacional','Internacional'=>'Internacional'],($viaje->nacional)?'Nacional':'Internacional', ['class' => 'form-control', 'style' => 'width: 100%','id'=>'nacional_id'])); ?>


                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">

                                                <div class="col-md-5">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('link', 'Link de la Reunión Científica')); ?>

                                                        <?php echo e(Form::text('link', $viaje->link, ['class' => 'form-control'])); ?>


                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">

                                                <div class="col-md-4">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('lugartrabajo', 'Lugar')); ?>

                                                        <?php echo e(Form::text('lugartrabajo', $viaje->lugartrabajo, ['class' => 'form-control'])); ?>


                                                    </div>
                                                </div>
                                                <div class="col-md-2">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('trabajodesde', 'Inicio')); ?>

                                                        <?php echo e(Form::date('trabajodesde', ($viaje->trabajodesde)?date('Y-m-d', strtotime($viaje->trabajodesde)):'', ['class' => 'form-control'])); ?>


                                                    </div>
                                                </div>
                                                <div class="col-md-2">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('trabajohasta', 'Fin')); ?>

                                                        <?php echo e(Form::date('trabajohasta', ($viaje->trabajohasta)?date('Y-m-d', strtotime($viaje->trabajohasta)):'', ['class' => 'form-control'])); ?>


                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('relevancia', 'Relevancia del evento (detalle las características del evento)')); ?>

                                                        <?php echo e(Form::textarea('relevancia', $viaje->relevancia, ['class' => 'form-control', 'rows' => 4])); ?>


                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">

                                                <div class="col-md-4">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('titulotrabajo', 'Título del Trabajo')); ?>

                                                        <?php echo e(Form::text('titulotrabajo', $viaje->titulotrabajo, ['class' => 'form-control'])); ?>


                                                    </div>
                                                </div>
                                                <div class="col-md-4">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('autores', 'Autores del Trabajo')); ?>

                                                        <?php echo e(Form::text('autores', $viaje->autores, ['class' => 'form-control'])); ?>


                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-8">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('resumen', 'Resumen del trabajo (mín. 300 palabras)')); ?>

                                                        <?php echo e(Form::textarea('resumen', $viaje->resumen, ['class' => 'form-control', 'rows' => 4])); ?>


                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">

                                                <div class="col-md-5">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('modalidad', 'Modalidad de la presentación')); ?>

                                                        <?php echo e(Form::text('modalidad', $viaje->modalidad, ['class' => 'form-control'])); ?>


                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="trabajo">Copia del Trabajo</label>
                                                        <input type="file" name="trabajo" class="form-control" placeholder="">
                                                        <?php if(!empty($viaje->trabajo)): ?>
                                                            <a href="<?php echo e(url($viaje->trabajo)); ?>" target="_blank">Descargar Copia del trabajo</a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="aceptacion">Aceptación</label>
                                                        <input type="file" name="aceptacion" class="form-control" placeholder="">
                                                        <?php if(!empty($viaje->aceptacion)): ?>
                                                            <a href="<?php echo e(url($viaje->aceptacion)); ?>" target="_blank">Descargar Aceptación</a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div id="divB" style="display: none">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="invitacion">Invitación del grupo receptor</label>
                                                        <input type="file" name="invitacion" class="form-control" placeholder="">
                                                        <?php if(!empty($viaje->invitacion)): ?>
                                                            <a href="<?php echo e(url($viaje->invitacion)); ?>" target="_blank">Descargar Invitación</a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="convenioB">Convenio</label>
                                                        <input type="file" name="convenioB" class="form-control" placeholder="">
                                                        <?php if(!empty($viaje->convenioB)): ?>
                                                            <a href="<?php echo e(url($viaje->convenioB)); ?>" target="_blank">Descargar Convenio</a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label for="aval">Aval de la entidad receptora firmado por una autoridad del centro donde se realizará el trabajo
                                                        </label>
                                                        <input type="file" name="aval" class="form-control" placeholder="">
                                                        <?php if(!empty($viaje->aval)): ?>
                                                            <a href="<?php echo e(url($viaje->aval)); ?>" target="_blank">Descargar Aval</a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>


                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('resumen', 'PLAN DE TRABAJO DE INVESTIGACION (para los tipo B)')); ?>



                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('generalB', '1. Objetivo general de la estadía')); ?>

                                                        <?php echo e(Form::textarea('generalB', $viaje->generalB, ['class' => 'form-control', 'rows' => 4])); ?>


                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('especificoB', '2. Objetivos específicos de la estadía')); ?>

                                                        <?php echo e(Form::textarea('especificoB', $viaje->especificoB, ['class' => 'form-control', 'rows' => 4])); ?>


                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('actividadesB', '3. Plan de trabajo de investigación a realizar en el período')); ?>

                                                        <?php echo e(Form::textarea('actividadesB', $viaje->actividadesB, ['class' => 'form-control', 'rows' => 4])); ?>


                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('cronogramaB', '4. Cronograma')); ?>

                                                        <?php echo e(Form::textarea('cronogramaB', $viaje->cronogramaB, ['class' => 'form-control', 'rows' => 4])); ?>


                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('justificacionB', '5. Justificación de la realización de la estadía y relación con el proyecto de investigación en el que participa')); ?>

                                                        <?php echo e(Form::textarea('justificacionB', $viaje->justificacionB, ['class' => 'form-control', 'rows' => 4])); ?>


                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <strong>6. Relevancia Institucional:
                                                        <ul class="desc">
                                                            <li>La afinidad y los aportes del grupo receptor a la línea de investigación del solicitante</li>
                                                            <li>La correspondencia del plan de trabajo a realizar con la línea de investigación del solicitante así como su factibilidad </li>
                                                            <li>Los aportes del desarrollo del plan de trabajo a la línea de investigación del solicitante</li>
                                                            <li>La transferencia que realizará el solicitante a su equipo de investigación, Unidad de Investigación y/o Unidad Académica a partir de la realización de su estadía</li>

                                                        </ul>
                                                        </strong>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">

                                                    <div class="form-group">

                                                        <?php echo e(Form::textarea('aportesB', $viaje->aportesB, ['class' => 'form-control', 'rows' => 4])); ?>


                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('relevanciaB', '7. Relevancia del lugar donde realiza la estadía. Justifique la elección del lugar')); ?>

                                                        <?php echo e(Form::textarea('relevanciaB', $viaje->relevanciaB, ['class' => 'form-control', 'rows' => 4])); ?>


                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="divC" style="display: none">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="cvprofesor">Currículum del profesor visitante</label>
                                                        <input type="file" name="cvprofesor" class="form-control" placeholder="">
                                                        <?php if(!empty($viaje->cvprofesor)): ?>
                                                            <a href="<?php echo e(url($viaje->cvprofesor)); ?>" target="_blank">Descargar Invitación</a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="convenioC">Convenio</label>
                                                        <input type="file" name="convenioC" class="form-control" placeholder="">
                                                        <?php if(!empty($viaje->convenioC)): ?>
                                                            <a href="<?php echo e(url($viaje->convenioC)); ?>" target="_blank">Descargar Convenio</a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">

                                                <div class="col-md-4">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('profesor', 'Profesor Visitante')); ?>

                                                        <?php echo e(Form::text('profesor', $viaje->profesor, ['class' => 'form-control'])); ?>


                                                    </div>
                                                </div>
                                                <div class="col-md-4">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('lugarprofesor', 'Lugar de Origen del Profesor Visitante')); ?>

                                                        <?php echo e(Form::text('lugarprofesor', $viaje->lugarprofesor, ['class' => 'form-control'])); ?>


                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('resumen', 'PLAN DE TRABAJO DE INVESTIGACION (para los tipo C)')); ?>



                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('objetivosC', '1. Objetivo de investigación de la estadía')); ?>

                                                        <?php echo e(Form::textarea('objetivosC', $viaje->objetivosC, ['class' => 'form-control', 'rows' => 4])); ?>


                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('planC', '2. Plan de trabajo de investigación a realizar en el período')); ?>

                                                        <?php echo e(Form::textarea('planC', $viaje->planC, ['class' => 'form-control', 'rows' => 4])); ?>


                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('relacionProyectoC', '3. Relación del plan de trabajo del investigador invitado con el proyecto de investigación acreditado del grupo receptor')); ?>

                                                        <?php echo e(Form::textarea('relacionProyectoC', $viaje->relacionProyectoC, ['class' => 'form-control', 'rows' => 4])); ?>


                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('aportesC', '4. Aportes del desarrollo del plan de trabajo al grupo de investigación, Unidad de Investigación y/o Unidad Académica')); ?>

                                                        <?php echo e(Form::textarea('aportesC', $viaje->aportesC, ['class' => 'form-control', 'rows' => 4])); ?>


                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">

                                                    <div class="form-group">
                                                        <?php echo e(Form::label('actividadesC', '5. Otras actividades')); ?>

                                                        <?php echo e(Form::textarea('actividadesC', $viaje->actividadesC, ['class' => 'form-control', 'rows' => 4])); ?>


                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="presupuesto">
                                        <?php $__currentLoopData = $tipoPresupuestos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipoPresupuesto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                                <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;"><?php echo e($tipoPresupuesto->nombre); ?></legend>

                                                <div class="form-group col-md-12">

                                                    <div class="table-responsive">
                                                        <table class="table" style="width: 70%">
                                                            <thead>


                                                            <th>Fecha</th>
                                                            <th>Descripción/Concepto</th>
                                                            <th>Importe</th>

                                                            <th><a href="#" class="addRowPresupuesto" data-tipo-id="<?php echo e($tipoPresupuesto->id); ?>"><i class="glyphicon glyphicon-plus"></i></a></th>
                                                            </thead>
                                                            <tbody class="cuerpoPresupuesto" data-tipo-id="<?php echo e($tipoPresupuesto->id); ?>">
                                                            <?php if(old('presupuesto'.$tipoPresupuesto->id.'fechas')): ?>
                                                                <?php $__currentLoopData = old('presupuesto'.$tipoPresupuesto->id.'fechas'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $fecha): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <tr>
                                                                        <td>
                                                                            <?php echo e(Form::date('presupuesto'.$tipoPresupuesto->id.'fechas[]', $fecha, ['class' => 'form-control', 'style' => 'width:150px;'])); ?>

                                                                        </td>
                                                                        <?php if($tipoPresupuesto->id == 2): ?>
                                                                            <td>
                                                                                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px;">
                                                                                    <?php echo e(Form::select('presupuesto'.$tipoPresupuesto->id.'conceptos[]',
                                                                                        [''=>'','Viaticos'=>'Viáticos','Pasajes'=>'Pasajes','Inscripcion'=>'Inscripción','Otros'=>'Otros'],
                                                                                        old('presupuesto'.$tipoPresupuesto->id.'conceptos.'.$index),
                                                                                        ['class' => 'form-control', 'style' => 'width: 120px', 'onchange' => 'seleccionarConcepto(this)']
                                                                                    )); ?>

                                                                                    <div class="extra-fields" style="display: flex; gap: 10px; align-items: center;">
                                                                                        <?php if(old('presupuesto'.$tipoPresupuesto->id.'conceptos.'.$index) === 'Viaticos'): ?>
                                                                                            <?php echo e(Form::text('presupuesto'.$tipoPresupuesto->id.'dias[]', old('presupuesto'.$tipoPresupuesto->id.'dias.'.$index),
                                                                                                ['class' => 'form-control ds_dias', 'placeholder' => 'Días', 'style' => 'width:150px']
                                                                                            )); ?>

                                                                                            <?php echo e(Form::text('presupuesto'.$tipoPresupuesto->id.'lugar[]', old('presupuesto'.$tipoPresupuesto->id.'lugar.'.$index),
                                                                                                ['class' => 'form-control ds_lugar', 'placeholder' => 'Lugar', 'style' => 'width:150px']
                                                                                            )); ?>

                                                                                        <?php elseif(old('presupuesto'.$tipoPresupuesto->id.'conceptos.'.$index) === 'Pasajes'): ?>
                                                                                            <?php echo e(Form::select('presupuesto'.$tipoPresupuesto->id.'pasajes[]',
                                                                                                ['' => '', 'Aereo' => 'Aéreo', 'Omnibus' => 'Omnibus', 'Automovil' => 'Automóvil'],
                                                                                                old('presupuesto'.$tipoPresupuesto->id.'pasajes.'.$index),
                                                                                                ['class' => 'form-control ds_pasajes', 'style' => ' width:120px']
                                                                                            )); ?>

                                                                                            <?php echo e(Form::text('presupuesto'.$tipoPresupuesto->id.'destino[]', old('presupuesto'.$tipoPresupuesto->id.'destino.'.$index),
                                                                                                ['class' => 'form-control ds_destino', 'placeholder' => 'Destino', 'style' => 'width:150px']
                                                                                            )); ?>

                                                                                        <?php elseif(old('presupuesto'.$tipoPresupuesto->id.'conceptos.'.$index) === 'Inscripcion'): ?>
                                                                                            <?php echo e(Form::text('presupuesto'.$tipoPresupuesto->id.'inscripcion[]', old('presupuesto'.$tipoPresupuesto->id.'inscripcion.'.$index),
                                                                                                ['class' => 'form-control ds_inscripcion', 'placeholder' => 'Descripción', 'style' => 'width:150px']
                                                                                            )); ?>

                                                                                        <?php elseif(old('presupuesto'.$tipoPresupuesto->id.'conceptos.'.$index) === 'Otros'): ?>
                                                                                            <?php echo e(Form::text('presupuesto'.$tipoPresupuesto->id.'otros[]', old('presupuesto'.$tipoPresupuesto->id.'otros.'.$index),
                                                                                                ['class' => 'form-control ds_otros', 'placeholder' => 'Otros', 'style' => 'width:150px']
                                                                                            )); ?>

                                                                                        <?php endif; ?>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        <?php else: ?>
                                                                            <td>
                                                                                <?php echo e(Form::text('presupuesto'.$tipoPresupuesto->id.'detalles[]', old('presupuesto'.$tipoPresupuesto->id.'detalles.'.$index), ['class' => 'form-control', 'style' => 'width: 400px'])); ?>

                                                                            </td>
                                                                        <?php endif; ?>
                                                                        <td>
                                                                            <?php echo e(Form::number('presupuesto'.$tipoPresupuesto->id.'importes[]', old('presupuesto'.$tipoPresupuesto->id.'importes.'.$index), ['class' => 'form-control', 'style' => 'width:150px;'])); ?>

                                                                        </td>
                                                                        <td>
                                                                            <a href="#" class="btn btn-danger removePresupuesto">
                                                                                <i class="glyphicon glyphicon-remove"></i>
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            <?php else: ?>
                                                                <?php $__currentLoopData = $viaje->presupuestos->where('tipo_presupuesto_id', $tipoPresupuesto->id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $presupuesto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php
                                                                        $detalles = explode('|', $presupuesto->detalle);
                                                                        $concepto = $detalles[0];
                                                                    ?>
                                                                    <tr>
                                                                        <td>
                                                                            <?php echo e(Form::date('presupuesto'.$tipoPresupuesto->id.'fechas[]', ($presupuesto->fecha)?date('Y-m-d', strtotime($presupuesto->fecha)):'', ['class' => 'form-control', 'style' => 'width:150px;'])); ?>

                                                                            <?php echo e(Form::hidden('presupuesto'.$tipoPresupuesto->id.'ids[]',$presupuesto->id)); ?>

                                                                        </td>
                                                                        <?php if($tipoPresupuesto->id == 2): ?>
                                                                            <td>
                                                                                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px;">
                                                                                    <?php echo e(Form::select('presupuesto'.$tipoPresupuesto->id.'conceptos[]',
                                                                                        [''=>'','Viaticos'=>'Viáticos','Pasajes'=>'Pasajes','Inscripcion'=>'Inscripción','Otros'=>'Otros'],
                                                                                        $concepto,
                                                                                        ['class' => 'form-control', 'style' => 'width: 120px', 'onchange' => 'seleccionarConcepto(this)']
                                                                                    )); ?>

                                                                                    <div class="extra-fields" style="display: flex; gap: 10px; align-items: center;">
                                                                                        <?php if($concepto === 'Viaticos'): ?>
                                                                                            <?php echo e(Form::text('presupuesto'.$tipoPresupuesto->id.'dias[]', $detalles[1], ['class' => 'form-control ds_dias', 'placeholder' => 'Días', 'style' => 'width:150px'])); ?>

                                                                                            <?php echo e(Form::text('presupuesto'.$tipoPresupuesto->id.'lugar[]', $detalles[2], ['class' => 'form-control ds_lugar', 'placeholder' => 'Lugar', 'style' => 'width:150px'])); ?>

                                                                                        <?php elseif($concepto === 'Pasajes'): ?>
                                                                                            <?php echo e(Form::select('presupuesto'.$tipoPresupuesto->id.'pasajes[]',
                                                                                                ['' => '', 'Aereo' => 'Aéreo', 'Omnibus' => 'Omnibus', 'Automovil' => 'Automóvil'],
                                                                                                $detalles[1],
                                                                                                ['class' => 'form-control ds_pasajes', 'style' => ' width:120px']
                                                                                            )); ?>

                                                                                            <?php echo e(Form::text('presupuesto'.$tipoPresupuesto->id.'destino[]', $detalles[2], ['class' => 'form-control ds_destino', 'placeholder' => 'Destino', 'style' => 'width:150px'])); ?>

                                                                                        <?php elseif($concepto === 'Inscripcion'): ?>
                                                                                            <?php echo e(Form::text('presupuesto'.$tipoPresupuesto->id.'inscripcion[]', $detalles[1], ['class' => 'form-control ds_inscripcion', 'placeholder' => 'Descripción', 'style' => 'width:150px'])); ?>

                                                                                        <?php elseif($concepto === 'Otros'): ?>
                                                                                            <?php echo e(Form::text('presupuesto'.$tipoPresupuesto->id.'otros[]', $detalles[1], ['class' => 'form-control ds_otros', 'placeholder' => 'Otros', 'style' => 'width:150px'])); ?>

                                                                                        <?php endif; ?>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        <?php else: ?>
                                                                            <td>
                                                                                <?php echo e(Form::text('presupuesto'.$tipoPresupuesto->id.'detalles[]', $detalles[0], ['class' => 'form-control', 'style' => 'width: 400px'])); ?>

                                                                            </td>
                                                                        <?php endif; ?>
                                                                        <td>
                                                                            <?php echo e(Form::number('presupuesto'.$tipoPresupuesto->id.'importes[]', $presupuesto->monto, ['class' => 'form-control', 'style' => 'width:150px;'])); ?>

                                                                        </td>
                                                                        <td>
                                                                            <a href="#" class="btn btn-danger removePresupuesto">
                                                                                <i class="glyphicon glyphicon-remove"></i>
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            <?php endif; ?>
                                                            </tbody>
                                                            <tfoot>
                                                            <tr>
                                                                <td colspan="2" style="text-align: right;">Subtotal:</td>
                                                                <td><input type="text" class="form-control subtotalPresupuesto" readonly style="width:150px;"></td>
                                                            </tr>
                                                            </tfoot>



                                                        </table>
                                                    </div>
                                                </div>

                                            </fieldset>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <label for="totalGeneral">Total:</label>
                                            <input type="text" id="totalGeneral" class="form-control" readonly style="width: 150px;">
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="lugar">

                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Para los subsidios tipo A ó B ingrese el lugar donde realizará la actividad, si el subsidio es tipo C ingrese el lugar de procedencia del investigador invitado</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                    <table class="table" style="width: 70%">
                                                        <thead>


                                                        <th>Institución</th>
                                                        <th>Ciudad</th>
                                                        <th>País</th>
                                                        <th>Desde</th>
                                                        <th>Desde</th>

                                                        <th><a href="#" class="addRowLugar"><i class="glyphicon glyphicon-plus"></i></a></th>
                                                        </thead>
                                                        <tbody id="cuerpoLugar">



                                                        <?php $__currentLoopData = $viaje->ambitos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $ambito): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <tr>
                                                                
                                                                <td><?php echo e(Form::text('ambitoinstitucions[]', $ambito->institucion ?? old("ambitoinstitucions.$index"), ['class' => 'form-control', 'style' => 'width: 450px'])); ?></td>
                                                                <td><?php echo e(Form::text('ambitociudads[]', $ambito->ciudad ?? old("ambitociudads.$index"), ['class' => 'form-control', 'style' => 'width: 200px'])); ?></td>
                                                                <td><?php echo e(Form::text('ambitopais[]', $ambito->pais ?? old("ambitopais.$index"), ['class' => 'form-control', 'style' => 'width: 200px'])); ?></td>
                                                                <td><?php echo e(Form::date('ambitodesdes[]', ($ambito->desde)?date('Y-m-d', strtotime($ambito->desde)):'' ?? old("ambitodesdes.$index"), ['class' => 'form-control', 'style' => 'width:150px;'])); ?></td>
                                                                <td><?php echo e(Form::date('ambitohastas[]', ($ambito->hasta)?date('Y-m-d', strtotime($ambito->hasta)):'' ?? old("ambitohastas.$index"), ['class' => 'form-control', 'style' => 'width:150px;'])); ?></td>
                                                                <td><a href="#" class="btn btn-danger removeLugar"><i class="glyphicon glyphicon-remove"></i></a></td>
                                                            </tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </tbody>




                                                    </table>
                                                </div>
                                            </div>

                                        </fieldset>


                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="montos">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('monto', 'MONTO SOLICITADO A LA UNLP (indicar el monto total en pesos)')); ?>



                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-1">
                                                <div class="form-group">

                                                    <?php echo e(Form::number('monto',$viaje->monto, ['class' => 'form-control'])); ?>


                                                </div>
                                            </div>
                                        </div>
                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">MONTO SOLICITADO A OTROS ORGANISMOS</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                    <table class="table" style="width: 70%">
                                                        <thead>


                                                        <th>Institución</th>
                                                        <th>Carácter</th>
                                                        <th>Importe</th>


                                                        <th><a href="#" class="addRowMontos"><i class="glyphicon glyphicon-plus"></i></a></th>
                                                        </thead>
                                                        <tbody id="cuerpoMontos">
                                                        <?php $__currentLoopData = $viaje->montos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $monto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <tr>
                                                                <!-- Institución -->
                                                                <td>
                                                                    <?php echo e(Form::text('montoinstitucions[]', $monto->institucion ?? old("montoinstitucions.$index"), ['class' => 'form-control', 'style' => 'width: 450px'])); ?>

                                                                </td>

                                                                <!-- Carácter -->
                                                                <td>
                                                                    <?php echo e(Form::text('montocaracters[]', $monto->caracter ?? old("montocaracters.$index"), ['class' => 'form-control', 'style' => 'width: 200px'])); ?>

                                                                </td>

                                                                <!-- Importe -->
                                                                <td>
                                                                    <?php echo e(Form::number('montomontos[]', $monto->importe ?? old("montomontos.$index"), ['class' => 'form-control', 'style' => 'width:150px;'])); ?>

                                                                </td>

                                                                <td>
                                                                    <a href="#" class="btn btn-danger removeMontos"><i class="glyphicon glyphicon-remove"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </tbody>





                                                    </table>
                                                </div>
                                            </div>

                                        </fieldset>


                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                    <a href='<?php echo e(route('viajes.index')); ?>' class="btn btn-warning">Volver</a>
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
<?php $__env->stopSection(); ?>
<?php $__env->startSection('footerSection'); ?>
    <!-- jQuery 3 -->
    <script src="<?php echo e(asset('bower_components/jquery/dist/jquery.min.js')); ?>"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="<?php echo e(asset('bower_components/bootstrap/dist/js/bootstrap.min.js')); ?>"></script>
    <!-- SlimScroll -->
    <script src="<?php echo e(asset('bower_components/jquery-slimscroll/jquery.slimscroll.min.js')); ?>"></script>
    <!-- FastClick -->
    <script src="<?php echo e(asset('bower_components/fastclick/lib/fastclick.js')); ?>"></script>
    <!-- Select2 -->
    <script src="<?php echo e(asset('bower_components/select2/dist/js/select2.min.js')); ?>"></script>
    <!-- Inputmask -->
    <script src="<?php echo e(asset('bower_components/inputmask/dist/min/jquery.inputmask.bundle.min.js')); ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo e(asset('dist/js/adminlte.min.js')); ?>"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="<?php echo e(asset('dist/js/demo.js')); ?>"></script>
    <!-- page script -->
    <script>
        $(document).ready(function () {
            $('#cuil').inputmask('99-99999999-9', { placeholder: 'XX-XXXXXXXX-X' });
            $('.js-example-basic-single').select2();

            updateTotals();
// Ocultar divMaterias por defecto
            //$('#divMaterias').hide();

            // Mostrar u ocultar divMaterias según la selección del select
            $(document).on('change', 'select[name="titulos[]"]', function() {
                if ($(this).val() !== '') {
                    $('#divMaterias').hide(); // Ocultar divMaterias si se selecciona algo en el select
                } else {
                    $('#divMaterias').show(); // Mostrar divMaterias si se selecciona vacío
                }
            });

            // Limpiar el estado del radio button por defecto
            $('input[name="actual"]').prop('checked', false);

            // Seleccionar el radio button por defecto
            $('#actual_1').prop('checked', true);

            // Limpiar el estado del radio button por defecto
            $('input[name="catactual"]').prop('checked', false);

            // Seleccionar el radio button por defecto
            $('#catactual_1').prop('checked', true);

            // Limpiar el estado del radio button por defecto
            $('input[name="sicadiactual"]').prop('checked', false);

            // Seleccionar el radio button por defecto
            $('#sicadiactual_1').prop('checked', true);

            // Bandera para rastrear si se han realizado cambios en el formulario
            var cambiosRealizados = false;

            // Escuchar cambios en los campos del formulario
            $('input, select, textarea').on('change', function() {
                cambiosRealizados = true;
            });

            // Mostrar mensaje de advertencia cuando el usuario intente abandonar la página
            window.addEventListener('beforeunload', function (event) {
                // Verificar si se han realizado cambios en el formulario
                if (cambiosRealizados) {
                    // Personaliza el mensaje de advertencia según tus necesidades
                    var mensaje = "¡Atención! Puede perder algunos cambios. ¿Estás seguro de abandonar la página?";

                    // Establecer el mensaje de advertencia en el evento
                    event.returnValue = mensaje;

                    // Devolver el mensaje de advertencia (solo necesario en algunos navegadores antiguos)
                    return mensaje;
                }
            });

            // Escuchar el envío del formulario
            $('form').on('submit', function() {
                // Establecer cambios realizados como false
                cambiosRealizados = false;
            });

        });




        $(document).on('change', 'select.institucionActual_select', function() {
            var rowIndex = $(this).closest('tr').index(); // Obtener el índice de la fila actual
            var institucionSeleccionada = $(this).val(); // Obtener la institución seleccionada

            // Obtener el select de becas en la misma fila
            var becasSelect = $('tbody#cuerpoBecaActual tr:eq(' + rowIndex + ') select.becaActual_select');
            var opciones = obtenerOpcionesBecaPorInstitucion(institucionSeleccionada); // Obtener opciones de beca

            // Limpiar el select de becas y agregar las nuevas opciones
            becasSelect.empty();
            opciones.forEach(function(opcion) {
                //console.log(opcion);
                becasSelect.append($('<option>', {
                    value: opcion,
                    text: opcion
                }));
            });
        });

        // Al cambiar cualquier select de instituciones
        $(document).on('change', 'select.institucionActual_select', function() {
            var rowIndex = $(this).closest('tr').index(); // Obtener el índice de la fila actual
            var institucionSeleccionada = $(this).val(); // Obtener la institución seleccionada

            // Obtener el select de becas en la misma fila
            var becasSelect = $('tbody#cuerpoBecas tr:eq(' + rowIndex + ') select.becaActual_select');
            var opciones = obtenerOpcionesBecaPorInstitucion(institucionSeleccionada); // Obtener opciones de beca

            // Limpiar el select de becas y agregar las nuevas opciones
            becasSelect.empty();
            opciones.forEach(function(opcion) {
                //console.log(opcion);
                becasSelect.append($('<option>', {
                    value: opcion,
                    text: opcion
                }));
            });
        });

        // Función para obtener opciones de beca según la institución seleccionada
        function obtenerOpcionesBecaPorInstitucion(institucionSeleccionada) {
            //console.log(institucionSeleccionada)
            var opciones = <?php echo json_encode(config('becas'), 15, 512) ?>;
            if (opciones[institucionSeleccionada]) {
                return opciones[institucionSeleccionada];
            }
            return ['']; // Opción por defecto
        }





        $('.addRowPresupuesto').on('click',function(e){
            e.preventDefault();
            var tipoId = $(this).data('tipo-id'); // Obtener el tipo de presupuesto
            //console.log(tipoId);
            addRowPresupuesto(tipoId);
            // Llama a updateTotals tras agregar una fila
            updateTotals();
        });
        function addRowPresupuesto(tipoId)
        {
            var tr = '';

            // Diferente lógica para tipoId 2
            if (tipoId == 2) {
                tr = '<tr>' +

                    '<td><input type="date" name="presupuesto' + tipoId + 'fechas[]" class="form-control" style="width: 150px;"></td>' +
                    '<td>' +

                    '<div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px;"><select name="presupuesto' + tipoId + 'conceptos[]" class="form-control" onchange="seleccionarConcepto(this)" style="width: 120px;">' +
                    '<option value="">-- seleccionar --</option>' +
                    '<option value="Viaticos">Viáticos</option>' +
                    '<option value="Pasajes">Pasajes</option>' +
                    '<option value="Inscripcion">Inscripción</option>' +
                    '<option value="Otros">Otros</option>' +
                    '</select>' +
                    // Div para campos adicionales (extraFields)
                    '<div class="extra-fields" style="display: flex; gap: 10px; align-items: center;">' +
                    '<input type="text" name="presupuesto' + tipoId + 'dias[]" class="form-control ds_dias" placeholder="Días" style="display:none; width: 150px; margin-top: 5px;">' +
                    '<input type="text" name="presupuesto' + tipoId + 'lugar[]" class="form-control ds_lugar" placeholder="Lugar" style="display:none; width: 150px; margin-top: 5px;">' +

                    '<select name="presupuesto' + tipoId + 'pasajes[]" class="form-control ds_pasajes" style="display:none; width: 150px; margin-top: 5px;">' +
                    '<option value="">-- seleccionar --</option>' +
                    '<option value="Aereo">Aéreo</option>' +
                    '<option value="Omnibus">Omnibus</option>' +
                    '<option value="Automovil">Automóvil</option>' +
                    '</select>' +

                    '<input type="text" name="presupuesto' + tipoId + 'destino[]" class="form-control ds_destino" placeholder="Destino" style="display:none; width: 150px; margin-top: 5px;">' +
                    '<input type="text" name="presupuesto' + tipoId + 'inscripcion[]" class="form-control ds_inscripcion" placeholder="Descripción" style="display:none; width: 150px; margin-top: 5px;">' +
                    '<input type="text" name="presupuesto' + tipoId + 'otros[]" class="form-control ds_otros" placeholder="Otros" style="display:none; width: 150px; margin-top: 5px;">' +
                    '</div>' + // Cierre de extra-fields
                    '</div>' + // Cierre del div principal
                    '</td>' +
                    '<td><input type="number" name="presupuesto' + tipoId + 'importes[]" class="form-control" style="width: 150px;"></td>' +
                    '<td><a href="#" class="btn btn-danger removePresupuesto"><i class="glyphicon glyphicon-remove"></i></a></td>' +

                    '</tr>';
            } else {
                // Lógica por defecto para otros tipos
                tr = '<tr>' +
                    '<td><input type="date" name="presupuesto' + tipoId + 'fechas[]" class="form-control" style="width: 150px;"></td>' +
                    '<td><input type="text" name="presupuesto' + tipoId + 'detalles[]" class="form-control" style="width: 400px;"></td>' +
                    '<td><input type="number" name="presupuesto' + tipoId + 'importes[]" class="form-control" style="width: 150px;"></td>' +
                    '<td><a href="#" class="btn btn-danger removePresupuesto"><i class="glyphicon glyphicon-remove"></i></a></td>' +
                    '</tr>';
            }

            // Agregar la fila al tbody correspondiente
            $('.cuerpoPresupuesto[data-tipo-id="' + tipoId + '"]').append(tr);



        };

        $('body').on('click', '.removePresupuesto', function(e){

            e.preventDefault();
            var confirmDelete = confirm('¿Estás seguro?');

            if (confirmDelete) {
                $(this).parent().parent().remove();
                // Llama a updateTotals tras eliminar una fila
                updateTotals();

            }

        });


        // Función para mostrar/ocultar campos según el concepto seleccionado
        function seleccionarConcepto(select) {
            var value = select.value;
            var row = $(select).closest('tr');

            // Ocultar todos los campos primero
            row.find('.ds_dias, .ds_lugar, .ds_pasajes, .ds_destino, .ds_inscripcion, .ds_otros').hide();

            // Mostrar los campos en función del valor seleccionado
            if (value === 'Viaticos') {
                row.find('.ds_dias, .ds_lugar').show();
            } else if (value === 'Pasajes') {
                row.find('.ds_pasajes, .ds_destino').show();
            } else if (value === 'Inscripcion') {
                row.find('.ds_inscripcion').show();
            } else if (value === 'Otros') {
                row.find('.ds_otros').show();
            }
        }

        // Actualiza el subtotal y total general
        function updateTotals() {
            var totalGeneral = 0;

            // Para cada tipo de presupuesto, calcula su subtotal
            $('.cuerpoPresupuesto').each(function() {
                var subtotal = 0;
                $(this).find('input[name^="presupuesto"][name$="importes[]"]').each(function() {
                    var importe = parseFloat($(this).val()) || 0;
                    subtotal += importe;
                });

                // Muestra el subtotal en el campo correspondiente
                $(this).closest('table').find('.subtotalPresupuesto').val(subtotal.toFixed(2));

                // Acumula el subtotal en el total general
                totalGeneral += subtotal;
            });

            // Muestra el total general
            $('#totalGeneral').val(totalGeneral.toFixed(2));
        }

        // Llama a updateTotals cuando cambien los valores de "Importe"
        $('body').on('input', 'input[name^="presupuesto"][name$="importes[]"]', function() {
            updateTotals();
        });

        $('#motivo').change(function() {
            // Oculta todos los div inicialmente
            $('#divA, #divB, #divC').hide();

            // Muestra el div correspondiente según la selección
            const selectedOption = $(this).val();
            if (selectedOption === 'A) Reuniones Científicas') {
                $('#divA').show();
            } else if (selectedOption === 'B) Estadía de trabajo para investigar en ámbitos académicos externos a la UNLP') {
                $('#divB').show();
            } else if (selectedOption === 'C) ESTADÍA DE TRABAJO EN LA UNLP PARA UN INVESTIGADOR INVITADO') {
                $('#divC').show();
            }
        });

        // Dispara el evento change al cargar la página
        $('#motivo').change();

        function prepararEnvio() {

            // Habilitar todos los inputs deshabilitados antes de enviar
            $('#formCrear').find(':disabled').prop('disabled', false);


            return true; // Permitir el envío del formulario
        }

        $('.addRowLugar').on('click',function(e){

            e.preventDefault();
            addRowLugar();
        });
        function addRowLugar()
        {

            var tr='<tr>'+
                '<td>'+'<?php echo e(Form::text('ambitoinstitucions[]', '',['class' => 'form-control', 'style' => 'width: 450px'])); ?>'+'</td>'+
                '<td>'+'<?php echo e(Form::text('ambitociudads[]', '',['class' => 'form-control', 'style' => 'width: 200px'])); ?>'+'</td>'+
                '<td>'+'<?php echo e(Form::text('ambitopais[]', '',['class' => 'form-control', 'style' => 'width: 200px'])); ?>'+'</td>'+
                '<td>'+'<?php echo e(Form::date('ambitodesdes[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])); ?>'+'</td>'+
                '<td>'+'<?php echo e(Form::date('ambitohastas[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])); ?>'+'</td>'+

                '<td><a href="#" class="btn btn-danger removeLugar"><i class="glyphicon glyphicon-remove"></i></a></td>'+
                '</tr>';
            $('#cuerpoLugar').append(tr);



        };

        $('body').on('click', '.removeLugar', function(e){

            e.preventDefault();
            var confirmDelete = confirm('¿Estás seguro?');

            if (confirmDelete) {
                $(this).parent().parent().remove();
            }


        });

        $('.addRowMontos').on('click',function(e){

            e.preventDefault();
            addRowMontos();
        });
        function addRowMontos()
        {

            var tr='<tr>'+
                '<td>'+'<?php echo e(Form::text('montoinstitucions[]', '',['class' => 'form-control', 'style' => 'width: 450px'])); ?>'+'</td>'+
                '<td>'+'<?php echo e(Form::text('montocaracters[]', '',['class' => 'form-control', 'style' => 'width: 200px'])); ?>'+'</td>'+

                '<td>'+'<?php echo e(Form::number('montomontos[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])); ?>'+'</td>'+


                '<td><a href="#" class="btn btn-danger removeMontos"><i class="glyphicon glyphicon-remove"></i></a></td>'+
                '</tr>';
            $('#cuerpoMontos').append(tr);



        };

        $('body').on('click', '.removeMontos', function(e){

            e.preventDefault();
            var confirmDelete = confirm('¿Estás seguro?');

            if (confirmDelete) {
                $(this).parent().parent().remove();
            }


        });

    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/sicadi/resources/views/viajes/edit.blade.php ENDPATH**/ ?>