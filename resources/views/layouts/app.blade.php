<!DOCTYPE html>
<html lang="en">

<head>

    @include('layouts.partials.head')

</head>
<body class="hold-transition skin-blue sidebar-mini">
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
