<nav class="navbar navbar-expand navbar-light bg-white">
    <a class="sidebar-toggle d-flex mr-2">
      <i class="hamburger align-self-center"></i>
    </a>

    <form class="form-inline d-none">
        <input class="form-control form-control-no-border mr-sm-2" type="text" placeholder="Search" aria-label="Search">
    </form>

    <div class="navbar-collapse collapse">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-toggle="dropdown">
                    <i class="align-middle" data-feather="settings"></i>
                </a>

                <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-toggle="dropdown">
                    <span class="text-dark">Admin</span>
                </a>

                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('topbar-logout-form').submit();">Sign out</a>
                    <form id="topbar-logout-form" action="{{ url('admin/logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
            </li>
        </ul>
    </div>
</nav>