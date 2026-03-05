<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Registro</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{asset('bower_components/bootstrap/dist/css/bootstrap.min.css')}}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('bower_components/font-awesome/css/font-awesome.min.css')}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{asset('bower_components/Ionicons/css/ionicons.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('dist/css/AdminLTE.min.css')}}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{asset('plugins/iCheck/square/blue.css')}}">
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <b>Plataforma de carga de datos</b><br>SECyT UNLP
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">{{ __('Register') }}</p>

            @include('includes.messages')
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group has-feedback">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="{{ __('Name') }}">

                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                            </div>


                        <div class="form-group has-feedback">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="{{ __('Email') }}">
                            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>

                            </div>
                        <div class="form-group has-feedback">
                            <input id="cuil" type="text" class="form-control @error('cuil') is-invalid @enderror" name="cuil" value="{{ old('cuil') }}" required autocomplete="cuil" autofocus placeholder="CUIL">

                            <span class="glyphicon glyphicon-info-sign form-control-feedback"></span>
                        </div>
                        <div class="form-group has-feedback">
                            <select name="facultad_id" class="form-control js-example-basic-single">
                                <option value="">Seleccionar UA</option>
                                @foreach($facultades as $facultadId => $facultad)
                                    <option value="{{ $facultadId }}" {{ old('facultad_id') == $facultadId ? 'selected' : '' }}>{{ $facultad }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group has-feedback">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="{{ __('Password') }}">
                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>

                            </div>


                        <div class="form-group has-feedback">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="{{ __('Confirm Password') }}">
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                            </div>

                        <div class="row mb-0">
                            <div class="col-xs-8">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                            <!-- /.col -->
                            <div class="col-xs-4">
                                <a href="{{ route('login') }}" class="btn btn-info">Volver</a>
                            </div>
                            <!-- /.col -->

                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- jQuery 3 -->
<script src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<!-- Inputmask -->
<script src="{{ asset('bower_components/inputmask/dist/min/jquery.inputmask.bundle.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
<!-- page script -->
<script>
    $(document).ready(function () {
        $('#cuil').inputmask('99-99999999-9', { placeholder: 'XX-XXXXXXXX-X' });
        $('.js-example-basic-single').select2();
    });
</script>
<div class="row" style="margin-top: -50px;">
    <div class="col-xs-12">
        <div style="text-align: center">
            <img src="{{ url('/images/secyt_unlp.PNG') }}" >
        </div>

    </div>
</div>
</body>
</html>
