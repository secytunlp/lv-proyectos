@extends('layouts.app')

@section('headSection')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.css') }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
           folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('dist/css/skins/_all-skins.min.css') }}">

@endsection


@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <i class="fa fa-user" aria-hidden="true"></i> Usuarios
                <!--<small>Create, Read, Update, Delete</small>-->
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('users.index') }}">Usuarios</a></li>
                <!--<li class="active">Data tables</li>-->
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Usuario</h3>
                            <a class='pull-right btn btn-success' href="{{ route('users.create') }}">Nuevo</a>
                        </div>
                        @include('includes.messages')


                    <!-- /.box-header -->
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Nro.</th>
                                    <th></th>
                                    <th>Nombre</th>
                                    <th>CUIL</th>
                                    <th>E-mail</th>
                                    <th>Roles</th>
                                    <th>Acciones</th>

                                </tr>
                                </thead>
                                <tbody>
        @foreach ($users as $user)
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                <td>
                    @if($user->image)
                        <img id="original" class="img-circle" src="{{ url('images/'.$user->image) }}" width="50px;">
                    @else
                        <img id="original" class="img-circle" src="{{ url('images/user.png') }}" >
                    @endif
                </td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->cuil }}</td>
                <td>{{ $user->email }}</td>
                <td>

                    @if(!empty($user->roles))

                        @foreach($user->roles as $v)

                            <label class="badge badge-success">{{ $v->name }}</label>
                        @endforeach
                    @endif
                </td>
                <td>@can('usuario-editar')<a href="{{ route('users.edit',$user->id) }}"><span class="glyphicon glyphicon-edit"></span></a>@endcan
                @can('usuario-eliminar')
                    <form id="delete-form-{{ $user->id }}" method="post" action="{{ route('users.destroy',$user->id) }}" style="display: none">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                    </form>
                    @endcan
                    <a href="" onclick="
                        if(confirm('Está seguro?'))
                        {
                        event.preventDefault();
                        document.getElementById('delete-form-{{ $user->id }}').submit();
                        }
                        else{
                        event.preventDefault();
                        }" ><span class="glyphicon glyphicon-trash"></span></a>
                </td>
            </tr>
        @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>Nro.</th>
                                    <th>Nombre</th>
                                    <th>Roles</th>
                                    <th>Editar</th>
                                    <th>Eliminar</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
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
    <!-- FastClick -->
    <script src="{{ asset('bower_components/fastclick/lib/fastclick.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('dist/js/demo.js') }}"></script>
    <!-- page script -->
    <script>
        $(document).ready(function() {
            $('#example1').DataTable({
                "autoWidth": false, // Desactiva el ajuste automático del anchos
                responsive: true,
                scrollX: true,
                "language": {
                    "url": "{{ asset('bower_components/datatables.net/lang/es-AR.json') }}"
                }
            });
        });

    </script>
@endsection