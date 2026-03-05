
<?php $__env->startSection('headSection'); ?>

    <!-- AdminLTE Skins. Choose a skin from the css/skins
           folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?php echo e(asset('dist/css/skins/_all-skins.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('bower_components/select2/dist/css/select2.min.css')); ?>">
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
</style>

<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <i class="fa fa-user-friends" aria-hidden="true"></i>Estado del integrante
                <small>Cambiar</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo e(route('home')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="<?php echo e(route('integrantes.index')); ?>">Integrantes</a></li>
                <li><a href="<?php echo e(route('integrante_estados.index')); ?>">Estados</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php if($integrante): ?><?php echo e($integrante->proyecto->codigo); ?> - <?php echo e($integrante->investigador->persona->apellido); ?> <?php echo e($integrante->investigador->persona->nombre); ?><?php endif; ?></h3>
                        </div>


                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="<?php echo e(route('integrante_estados.store')); ?>" method="post" enctype="multipart/form-data">
                            <?php echo e(csrf_field()); ?>

                            <div class="box-body">
                                <?php echo $__env->make('includes.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#datos_proyecto" aria-controls="datos_proyecto" role="tab" data-toggle="tab">Proyecto</a></li>

                                    <li role="presentation"><a href="#universidad" aria-controls="universidad" role="tab" data-toggle="tab">Universidad</a></li>
                                    <li role="presentation"><a href="#investigacion" aria-controls="investigacion" role="tab" data-toggle="tab">Investigación</a></li>
                                    <li role="presentation"><a href="#categorizacion" aria-controls="categorizacion" role="tab" data-toggle="tab">Categorización</a></li>
                                    <li role="presentation"><a href="#becario" aria-controls="becario" role="tab" data-toggle="tab">Becas</a></li>
                                    <!-- Agrega más pestañas según sea necesario -->
                                </ul>
                                <div class="tab-content" style="margin: 1%;">
                                    <div role="tabpanel" class="tab-pane active" id="datos_proyecto">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">

                                                    <?php echo e(Form::label('estado', 'Estado')); ?>

                                                    <?php echo e(Form::select('estado',[''=>'','Alta Creada'=>'Alta Creada','Alta Recibida'=>'Alta Recibida','Baja Creada'=>'Baja Creada','Baja Recibida'=>'Baja Recibida','Cambio Creado'=>'Cambio Creado','Cambio Recibido'=>'Cambio Recibido','Cambio Hs. Creado'=>'Cambio Hs. Creado','Cambio Hs. Recibido'=>'Cambio Hs. Recibido','Cambio Tipo Creado'=>'Cambio Tipo Creado','Cambio Tipo Recibido'=>'Cambio Tipo Recibido'], $integrante->estado,['class' => 'form-control'])); ?>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <input type="hidden" name="integrante_id" value="<?php echo e($integrante->id ?? ''); ?>">
                                                    <?php echo e(Form::label('tipo', 'Tipo')); ?>

                                                    <?php echo e(Form::select('tipo',[''=>'','Director'=>'Director','Codirector'=>'Codirector','Investigador Formado'=>'Investigador Formado','Investigador En Formación'=>'Investigador En Formación','Becario, Tesista'=>'Becario, Tesista','Colaborador'=>'Colaborador'], $integrante->tipo,['class' => 'form-control'])); ?>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('horas', 'Horas')); ?>

                                                    <?php echo e(Form::number('horas', $integrante->horas, ['class' => 'form-control','placeholder'=>'Horas'])); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">

                                                    <?php echo e(Form::label('alta', 'Alta')); ?>

                                                    <?php echo e(Form::date('alta', ($integrante->alta)?date('Y-m-d', strtotime($integrante->alta)):'',['class' => 'form-control'])); ?>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">

                                                    <?php echo e(Form::label('baja', 'Baja')); ?>

                                                    <?php echo e(Form::date('baja', ($integrante->baja)?date('Y-m-d', strtotime($integrante->baja)):'',['class' => 'form-control'])); ?>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">

                                                    <?php echo e(Form::label('cambio', 'Cambio')); ?>

                                                    <?php echo e(Form::date('cambio', ($integrante->cambio)?date('Y-m-d', strtotime($integrante->cambio)):'',['class' => 'form-control'])); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-8">

                                                <div class="form-group">
                                                    <?php echo e(Form::label('comentarios', 'Comentarios')); ?>

                                                    <?php echo e(Form::textarea('comentarios', '', ['class' => 'form-control'])); ?>


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

                                                    <td><?php echo e(Form::select('titulos[]',$titulos, $integrante->titulo_id,['class' => 'form-control js-example-basic-single', 'style' => 'width: 400px'])); ?></td>
                                                    <td><?php echo e(Form::date('egresos[]', ($integrante->egresogrado)?date('Y-m-d', strtotime($integrante->egresogrado)):'', ['class' => 'form-control', 'style' => 'width:150px;'])); ?></td>

                                                    <!--<td><a href="#" class="btn btn-danger remove"><i class="glyphicon glyphicon-remove"></i></a></td>-->
                                                </tr>

                                                </tbody>




                                            </table>
                                                </div>
                                        </div>

                                        </fieldset>

                                        <fieldset style="border: 1px solid #ccc; padding: 10px;" id="divMaterias">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Estudiante</legend>
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('carrera', 'Carrera')); ?>

                                                    <?php echo e(Form::text('carrera', $integrante->carrera, ['class' => 'form-control','placeholder'=>'Carrera'])); ?>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('total', 'Total De Materias')); ?>

                                                    <?php echo e(Form::number('total', $integrante->total, ['class' => 'form-control','placeholder'=>'Total De Materias'])); ?>


                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('materias', 'Aprobadas')); ?>

                                                    <?php echo e(Form::number('materias', $integrante->materias, ['class' => 'form-control','placeholder'=>'Aprobadas'])); ?>

                                                </div>
                                            </div>

                                        </div>
                                        </fieldset>
                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Título de Posgrado</legend>

                                            <div class="form-group col-md-12">
                                                <div class="table-responsive">

                                                <table class="table" style="width: 50%">
                                                    <thead>

                                                    <th>Título</th>
                                                    <th>Egreso</th>
                                                    <!--<th><a href="#" class="addRowPost"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                    </thead>

                                                    <tbody id="cuerpoPosgrado">
                                                    <tr>

                                                        <td><?php echo e(Form::select('tituloposts[]',$tituloposts, $integrante->titulopost_id,['class' => 'form-control js-example-basic-single', 'style' => 'width: 400px'])); ?></td>
                                                        <td><?php echo e(Form::date('egresoposts[]', ($integrante->egresoposgrado)?date('Y-m-d', strtotime($integrante->egresoposgrado)):'', ['class' => 'form-control', 'style' => 'width:150px;'])); ?></td>

                                                        <!--<td><a href="#" class="btn btn-danger removePost"><i class="glyphicon glyphicon-remove"></i></a></td>-->
                                                    </tr>

                                                    </tbody>




                                                </table>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Cargo Docente</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                <table class="table" style="width: 50%">
                                                    <thead>

                                                    <th>Cargo</th>
                                                    <th>Dedicación</th>
                                                    <th>Ingreso</th>
                                                    <th>U. Académica</th>
                                                    <th>Universidad</th>
                                                    <!--<th>Activo</th>
                                                    <th><a href="#" class="addRowCargo"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                    </thead>

                                                    <tbody id="cuerpoCargos">
                                                    <tr>

                                                        <td><?php echo e(Form::select('cargos[]',$cargos, $integrante->cargo_id,['class' => 'form-control', 'style' => 'width: 200px'])); ?></td>
                                                        <td><?php echo e(Form::select('deddocs[]',[''=>'','Exclusiva'=>'Exclusiva','Semi Exclusiva'=>'Semi Exclusiva','Simple'=>'Simple'], $integrante->deddoc,['class' => 'form-control', 'style' => 'width: 120px'])); ?></td>
                                                        <td><?php echo e(Form::date('ingresos[]', ($integrante->alta_cargo)?date('Y-m-d', strtotime($integrante->alta_cargo)):'', ['class' => 'form-control', 'style' => 'width:150px;'])); ?></td>
                                                        <td><?php echo e(Form::select('facultads[]',$facultades, $integrante->facultad_id,['class' => 'form-control', 'style' => 'width: 300px'])); ?></td>
                                                        <td><?php echo e(Form::select('universidads[]',$universidades, $integrante->universidad_id,['class' => 'form-control js-example-basic-single', 'style' => 'width: 300px'])); ?></td>
                                                        <!--<td><?php echo e(Form::checkbox('activos[]', 1,true)); ?></td>
                                                        <td><a href="#" class="btn btn-danger removeCargo"><i class="glyphicon glyphicon-remove"></i></a></td>-->
                                                    </tr>

                                                    </tbody>




                                                </table>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="investigacion">
                                        <div class="row">

                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('unidad', 'Lugar de Trabajo')); ?>

                                                    <?php echo e(Form::select('unidad_id',  $unidads,$integrante->unidad_id, ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'unidad_id'])); ?>


                                                </div>
                                            </div>


                                        </div>
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

                                                        <td><?php echo e(Form::select('carrerainvs[]',$carrerainvs, $integrante->carrrera_id,['class' => 'form-control', 'style' => 'width: 200px'])); ?></td>
                                                        <td><?php echo e(Form::select('organismos[]',$organismos, $integrante->organismo_id,['class' => 'form-control', 'style' => 'width: 150px'])); ?></td>
                                                        <td><?php echo e(Form::date('carringresos[]', ($integrante->ingreso_carrerainv)?date('Y-m-d', strtotime($integrante->ingreso_carrerainv)):'', ['class' => 'form-control', 'style' => 'width:150px;'])); ?></td>


                                                        <!--<td><?php echo e(Form::radio('actual', 1, true,['id' => 'actual_1'])); ?></td>
                                                        <td><a href="#" class="btn btn-danger removeCarrerainv"><i class="glyphicon glyphicon-remove"></i></a></td>-->
                                                    </tr>

                                                    </tbody>




                                                </table>
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
                                                    <!--<th>Año</th>
                                                    <th>Notificación</th>
                                                    <th>Universidad</th>
                                                    <th>Actual</th>
                                                    <th><a href="#" class="addRowCategoria"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                    </thead>

                                                    <tbody id="cuerpoCategorias">
                                                    <tr>

                                                        <td><?php echo e(Form::select('categorias[]',$categorias, $integrante->categoria_id,['class' => 'form-control', 'style' => 'width: 60px'])); ?></td>
                                                        <!--<td><?php echo e(Form::select('catyears[]',$years, '',['class' => 'form-control', 'style' => 'width: 60px'])); ?></td>
                                                        <td><?php echo e(Form::date('catnotificacions[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])); ?></td>
                                                        <td><?php echo e(Form::select('catuniversidads[]',$universidades, '',['class' => 'form-control js-example-basic-single', 'style' => 'width: 300px'])); ?></td>

                                                        <td><?php echo e(Form::radio('catactual', 1, true,['id' => 'catactual_1'])); ?></td>
                                                        <td><a href="#" class="btn btn-danger removeCategoria"><i class="glyphicon glyphicon-remove"></i></a></td>-->
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
                                                    <!--<th>Año</th>
                                                    <th>Notificación</th>

                                                  <th>Actual</th>
                                                    <th><a href="#" class="addRowSicadi"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                    </thead>

                                                    <tbody id="cuerpoSicadis">
                                                    <tr>

                                                        <td><?php echo e(Form::select('sicadis[]',$sicadis, $integrante->sicadi_id,['class' => 'form-control', 'style' => 'width: 120px'])); ?></td>
                                                        <!--<td><?php echo e(Form::select('sicadiyears[]',$years, '',['class' => 'form-control', 'style' => 'width: 60px'])); ?></td>
                                                        <td><?php echo e(Form::date('sicadinotificacions[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])); ?></td>


                                                        <td><?php echo e(Form::radio('sicadiactual', 1, true,['id' => 'sicadiactual_1'])); ?></td>
                                                        <td><a href="#" class="btn btn-danger removeSicadi"><i class="glyphicon glyphicon-remove"></i></a></td>-->
                                                    </tr>

                                                    </tbody>




                                                </table>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="becario">

                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Beca</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                <table class="table" style="width: 50%">
                                                    <thead>


                                                    <th>Institución</th>
                                                    <th>Beca</th>
                                                    <th>Desde</th>
                                                    <th>Hasta</th>
                                                    <!--<th>UNLP</th>
                                                    <th><a href="#" class="addRowBeca"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                    </thead>

                                                    <tbody id="cuerpoBecas">
                                                    <tr>

                                                        <td><?php echo e(Form::select('institucions[]',[''=>'','ANPCyT'=>'ANPCyT','CIC'=>'CIC','CONICET'=>'CONICET','UNLP'=>'UNLP','CIN'=>'CIN','OTRA'=>'OTRA'], $integrante->institucion,['class' => 'form-control institucion_select', 'style' => 'width: 150px'])); ?></td>
                                                        <td><?php echo e(Form::select('becas[]',[''=>'','Beca inicial'=>'Beca inicial','Beca superior'=>'Beca superior','Beca de entrenamiento'=>'Beca de entrenamiento','Beca doctoral'=>'Beca doctoral','Beca posdoctoral'=>'Beca posdoctoral','Beca finalización del doctorado'=>'Beca finalización del doctorado','Beca maestría'=>'Beca maestría','Formación Superior'=>'Formación Superior','Iniciación'=>'Iniciación','TIPO I'=>'TIPO I','TIPO II'=>'TIPO II','TIPO A'=>'TIPO A','Tipo A - Maestría'=>'Tipo A - Maestría','Tipo A - Doctorado'=>'Tipo A - Doctorado','Beca Cofinanciada (UNLP-CIC)'=>'Beca Cofinanciada (UNLP-CIC)','Especial de Maestría'=>'Especial de Maestría','TIPO B'=>'TIPO B','TIPO B (DOCTORADO)'=>'TIPO B (DOCTORADO)','TIPO B (MAESTRÍA)'=>'TIPO B (MAESTRÍA)','BECA DE PERFECCIONAMIENTO'=>'BECA DE PERFECCIONAMIENTO','CONICET 2'=>'CONICET 2','RETENCION DE POSTGRADUADO'=>'RETENCION DE POSTGRADUADO','EVC'=>'EVC'], $integrante->beca,['class' => 'form-control beca_select', 'style' => 'width: 150px'])); ?></td>

                                                        <td><?php echo e(Form::date('becadesdes[]', ($integrante->alta_beca)?date('Y-m-d', strtotime($integrante->alta_beca)):'', ['class' => 'form-control', 'style' => 'width:150px;'])); ?></td>

                                                        <td><?php echo e(Form::date('becahastas[]', ($integrante->baja_beca)?date('Y-m-d', strtotime($integrante->baja_beca)):'', ['class' => 'form-control', 'style' => 'width:150px;'])); ?></td>
                                                        <!--<td><?php echo e(Form::checkbox('becaunlps[]', 1,false)); ?></td>
                                                        <td><a href="#" class="btn btn-danger removeCategoria"><i class="glyphicon glyphicon-remove"></i></a></td>-->
                                                    </tr>

                                                    </tbody>




                                                </table>
                                                </div>
                                            </div>
                                        </fieldset>

                                    </div>
                                </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                        <a href="<?php echo e(route('integrante_estados.index')); ?>?integrante_id=<?php echo e($integrante->id); ?>" class="btn btn-warning">Volver</a>

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
    <!-- jQuery UI -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="<?php echo e(asset('dist/js/confirm-exit.js')); ?>"></script>
    <!-- page script -->
    <script>
        $(document).ready(function () {
            $('#cuil').inputmask('99-99999999-9', { placeholder: 'XX-XXXXXXXX-X' });
            $('.js-example-basic-single').select2();


// Ocultar divMaterias por defecto
            if ($('#cuerpoTitulo tr').length > 0) {
                $('#divMaterias').hide(); // Ocultar divMaterias si hay títulos presentes
            }

            // Mostrar u ocultar divMaterias según la selección del select
            $(document).on('change', 'select[name="titulos[]"]', function() {
                if ($(this).val() !== '') {
                    $('#divMaterias').hide(); // Ocultar divMaterias si se selecciona algo en el select
                } else {
                    $('#divMaterias').show(); // Mostrar divMaterias si se selecciona vacío
                }
            });


        });

        // Al cambiar cualquier select de instituciones
        $(document).on('change', 'select.institucion_select', function() {
            var rowIndex = $(this).closest('tr').index(); // Obtener el índice de la fila actual
            var institucionSeleccionada = $(this).val(); // Obtener la institución seleccionada

            // Obtener el select de becas en la misma fila
            var becasSelect = $('tbody#cuerpoBecas tr:eq(' + rowIndex + ') select.beca_select');
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
            // Verificar si opciones es null o undefined
            if (!opciones) {
                return ['']; // Opción por defecto si opciones es null o undefined
            }
            if (opciones[institucionSeleccionada]) {
                return opciones[institucionSeleccionada];
            }
            return ['']; // Opción por defecto
        }

    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/sicadi/resources/views/integrante_estados/create.blade.php ENDPATH**/ ?>