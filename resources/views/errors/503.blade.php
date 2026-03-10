@extends('layouts.guest')

@section('content')
    <div class="content-wrapper">

        <section class="content" style="margin-top:80px;">
            <div class="row">
                <div class="col-md-12">

                    @include('includes.messages')

                    <div class="box box-danger">

                        <div class="box-body text-center" style="padding:60px;">

                            <h1 style="font-size:38px;color:#c0392b;margin-bottom:25px;">
                                Plataforma en mantenimiento
                            </h1>

                            <p style="font-size:18px;">
                                La plataforma se encuentra temporalmente
                                fuera de servicio por tareas de actualización.
                            </p>

                            <p style="font-size:16px;margin-top:15px;">
                                Por favor intente nuevamente más tarde.
                            </p>

                            <hr style="width:50%;margin:30px auto;">

                            <p style="color:#666;">
                                Secretaría de Ciencia y Técnica<br>
                                Universidad Nacional de La Plata
                            </p>

                        </div>

                    </div>

                </div>
            </div>
        </section>

    </div>
@endsection
