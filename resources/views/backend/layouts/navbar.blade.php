<nav class="main-header navbar navbar-expand navbar-light glass-effect">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        @can('sale_create')
        <li class="nav-item mr-3">
            <a class="nav-link btn btn-primary text-white px-3" href="{{route('backend.admin.cart.index')}}">
                <i class="fas fa-shopping-cart mr-1"></i> POS
            </a>
        </li>
        @endcan

        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>

        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <img src="{{ auth()->user()->pro_pic }}" class="img-circle elevation-1" style="width: 32px; height: 32px; object-fit: cover; margin-top: -5px;" alt="User">
                <i class="fas fa-chevron-down ml-1" style="font-size: 10px; vertical-align: middle;"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-header text-left">
                    <p class="mb-0 font-weight-bold" style="color: var(--primary);">{{ auth()->user()->name }}</p>
                    <small class="text-muted">{{ auth()->user()->email }}</small>
                </div>
                <div class="dropdown-divider"></div>
                <a href="{{ route('backend.admin.profile') }}" class="dropdown-item">
                    <i class="fas fa-user-edit mr-2" style="color: var(--secondary);"></i>
                    My Profile
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('logout') }}" class="dropdown-item">
                    <i class="fas fa-sign-out-alt mr-2" style="color: #ff4d6d;"></i>
                    Sign Out
                </a>
            </div>
        </li>
    </ul>
</nav>