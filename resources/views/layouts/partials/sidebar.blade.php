  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- sidebar menu: : style can be found in sidebar.less -->
        @can('usuario-listar')<ul class="sidebar-menu" data-widget="tree">
          <li class="header">SEGURIDAD</li>

          @can('usuario-listar')<li><a href="{{ route('users.index') }}"><i class="fa fa-user"></i> Usuarios</a></li>@endcan
          @can('rol-listar')<li><a href="{{ route('roles.index') }}"><i class="fa fa-user-plus"></i> Roles</a></li>@endcan

      </ul>@endcan
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">CONFIGURACION</li>





        </ul>
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">ADMINISTRACION</li>



        </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
