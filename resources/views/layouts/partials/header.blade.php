  <header class="main-header">

    <!-- Logo -->
    <a href="{{ url('/') }}" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><img src="{{ url('/images/secyt.PNG') }}" width="100%"></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><img src="{{ url('/images/secyt.PNG') }}" width="100%"></span>
    </a>

      <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Navegación</span>
      </a>

      <div class="navbar-custom-menu">
          @if(auth()->check())
        <ul class="nav navbar-nav">

      <!-- User Account: style can be found in dropdown.less -->

          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                @if(Auth::user()->image)
              <img src="{{ url('/images/'.Auth::user()->image) }}" class="user-image" alt="User Image">
                @else
                    <img src="{{ url('/images/user.png') }}" class="user-image" alt="User Image">
                @endif
              <span class="hidden-xs">{{ Auth::user()->name }}</span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                  @if(Auth::user()->image)
                <img src="{{ url('/images/'.Auth::user()->image) }}" class="img-circle" alt="User Image">
                  @else
                      <img src="{{ url('/images/user.png') }}" class="img-circle" alt="User Image">
                  @endif
                <p>
                    <?php
                    $roles='';
                         if(!empty(Auth::user()->roles)){
                             foreach (Auth::user()->roles as $v){
                                 $roles .=$v->name.' - ';
                             }
                         }



                    ?>

                    {{ Auth::user()->name }}

                  <small>Miembro desde {{ date('d-m-Y', strtotime(Auth::user()->created_at)) }}</small>
                </p>
              </li>


              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="{{ route('users.perfil',array('idUser'=>Auth::user()->id)) }}" class="btn btn-default btn-flat">Perfil</a>
                </div>
                <div class="btn btn-default pull-right">
                      <a href="{{ route('logout') }}"
                          onclick="event.preventDefault();
                                   document.getElementById('logout-form').submit();">
                          Cerrar
                      </a>

                      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                          {{ csrf_field() }}
                      </form>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
          <!--<li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>-->
        </ul>
          @endif
      </div>
    </nav>

  </header>

