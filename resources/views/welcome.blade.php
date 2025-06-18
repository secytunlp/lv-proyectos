<!-- resources/views/welcome.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">

        </section>
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div style="text-align: center">
                        <img src="{{ url('/images/secyt_unlp.PNG') }}" >
                    </div>
                        <div class="box-body">

                        <div class="login-logo">
                            <b>Plataforma de carga de datos</b><br>SECyT UNLP

                        </div>

                    </div>
                </div>
            </div>

        </section>
    </div>
@endsection

@section('footerSection')
    <!-- jQuery 3 -->
    <script src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="{{ asset('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->


@endsection
