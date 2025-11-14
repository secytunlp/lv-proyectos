  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- sidebar menu: : style can be found in sidebar.less -->
        @if(auth()->user()->permissions->contains('name', 'usuario-listar'))<ul class="sidebar-menu" data-widget="tree">
          <li class="header">SEGURIDAD</li>

            @if(auth()->user()->permissions->contains('name', 'usuario-listar'))<li><a href="{{ route('users.index') }}"><i class="fa fa-user"></i> Usuarios</a></li>@endif
            @if(auth()->user()->permissions->contains('name', 'rol-listar'))<li><a href="{{ route('roles.index') }}"><i class="fa fa-user-plus"></i> Roles</a></li>@endif

      </ul>@endif
  {{-- @if(auth()->user()->permissions->contains('name', 'administrador-general')) --}}
  <ul class="sidebar-menu" data-widget="tree">
      <li class="header">CONFIGURACION</li>
      @if(auth()->user()->permissions->contains('name', 'universidad-listar'))<li><a href="{{ route('universidads.index') }}"><i class="fa fa-university"></i>Universidades</a></li>@endif


      @if(auth()->user()->permissions->contains('name', 'titulo-listar'))<li><a href="{{ route('titulos.index') }}"><i class="fa fa-graduation-cap"></i>Titulos</a></li>@endif


  </ul>
  @if((session('es_director')))
  <ul class="sidebar-menu" data-widget="tree">
      <li class="header">ADMINISTRACION</li>
      @if(auth()->user()->permissions->contains('name', 'investigador-listar'))<li><a href="{{ route('investigadors.index') }}"><i class="fa fa-microscope"></i>Investigadores</a></li>@endif
      @if(auth()->user()->permissions->contains('name', 'proyecto-listar'))<li><a href="{{ route('proyectos.index') }}"><i class="fa fa-cogs"></i>Proyectos</a></li>@endif
      @if(auth()->user()->permissions->contains('name', 'integrante-listar'))<li><a href="{{ route('integrantes.index') }}"><i class="fa fa-user-friends"></i>Integrantes</a></li>@endif

  </ul>
  @endif
  {{--@endif--}}
  <ul class="sidebar-menu" data-widget="tree">
      <li class="header">CONVOCATORIAS</li>
  {{-- @if(auth()->user()->permissions->contains('name', 'administrador-general'))--}}
       @if(auth()->user()->permissions->contains('name', 'solicitud-listar'))<li><a href="{{ route('jovens.index') }}"><i class="fa fa-microscope"></i>Jóvenes Investigadores</a></li>@endif
       @if(auth()->user()->permissions->contains('name', 'evaluacion-listar'))<li><a href="{{ route('joven_evaluacions.index') }}"><i class="fa fa-th-list"></i>Evaluaciones Jóvenes</a></li>@endif
       @if(auth()->user()->permissions->contains('name', 'solicitud-listar'))<li><a href="{{ route('viajes.index') }}"><i class="fa fa-plane"></i>Viajes/Estadías</a></li>@endif
       @if(auth()->user()->permissions->contains('name', 'evaluacion-listar'))<li><a href="{{ route('viaje_evaluacions.index') }}"><i class="fa fa-th-list"></i>Evaluaciones Viajes/Estadías</a></li>@endif
  {{--@endif--}}
  @if(auth()->user()->permissions->contains('name', 'solicitud_sicadi-listar'))<li><a href="{{ route('solicitud_sicadis.index') }}"><i class="fa fa-layer-group"></i>SICADI</a></li>@endif
</ul>
</section>
<!-- /.sidebar -->
</aside>


