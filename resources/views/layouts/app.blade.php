<!DOCTYPE html>
<html lang="en">

<head>

    @include('layouts.partials.head')

</head>
<body class="hold-transition skin-blue sidebar-mini">
@if (app()->environment('local'))
    <div style="background:#d9534f;color:white;padding:10px;text-align:center;font-weight:bold;">
        ⚠ ESTÁS UTILIZANDO EL ENTORNO DE DESARROLLO (DEV).
    </div>
@endif

<div class="wrapper">

    @include('layouts.partials.header')

    @include('layouts.partials.sidebar')

    @yield('content')

    @show


    @include('layouts.partials.footer')

</div>
<!-- ./wrapper -->

</body>
</html>
