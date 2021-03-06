<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="_token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatables.css') }}">
    @yield('css')
    <style>
        .hide-on-load {
            visibility: hidden;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/pace-js@latest/pace.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pace-js@latest/pace-theme-default.min.css">
    @yield('preload')
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
    <div class="wrapper">

        {{-- Header/Navbar --}}
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button" data-enable-remember="true"><i
                            class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item">
                    <a href="#" class="navbar-brand">Polytechnic University of the Philippines Calauan
                        Campus</a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <img src="{{ DDSS::getAvatar(Auth::user()->id, 50) }}" alt="" class="rounded-circle my-auto"
                    style="width: 15%; height: 15%">
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->full_name() }}
                    </a>

                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <a href="{{ route('user.profile') }}" class="dropdown-item">Profile</a>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                                     document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </nav>

        {{-- Sidebar --}}
        <aside class="main-sidebar sidebar-dark-primary elevation-4">

            <a href="#" class="brand-link" style="text-decoration: none">
                <img src="{{ url('/favicon.ico') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
                    style="opacity: 1">
                <span class="brand-text font-weight-light">PUPC</span>
            </a>

            <div class="sidebar">

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">

                        <li class="nav-item">
                            <a href="{{ route('home') }}" class="nav-link {{ Request::is('home') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-gauge"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>
                        </li>
                        @can('do-admin-stuff')
                        <li class="nav-item {{ Request::is('users', 'roles*', 'user*') ? 'menu-open' : 'menu-close' }}">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>
                                    User Management
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('user.index') }}"
                                        class="nav-link {{ Request::is('users', 'user*') ? 'active' : '' }}">
                                        <i class="fas fa-user-group nav-icon"></i>
                                        <p>Users</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('role.index') }}"
                                        class="nav-link {{ Request::is('roles*') ? 'active' : '' }}">
                                        <i class="fas fa-briefcase nav-icon"></i>
                                        <p>Roles</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endcan
                        <li
                            class="nav-item {{ Request::is('files*', 'shared_files', 'trash', 'share*') ? 'menu-open' : 'menu-close' }}">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-folder-tree"></i>
                                <p>
                                    File Management
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ Cookie::get('view') == 'grid' ? route('file.index').'?grid=1' : route('file.index') }}"
                                        class="nav-link {{ Request::is('files*') ? 'active' : '' }}">
                                        <i class="fas fa-file nav-icon"></i>
                                        <p>Files</p>
                                    </a>
                                </li>
                                @can('do-admin-stuff')
                                <li class="nav-item">
                                    <a href="{{ Cookie::get('tview') == 'grid' ? route('file.trash').'?grid=1' : route('file.trash') }}"
                                        class="nav-link {{ Request::is('trash') ? 'active' : '' }}">
                                        <i class="fas fa-trash nav-icon"></i>
                                        <p>Trash</p>
                                    </a>
                                </li>
                                @endcan
                                @cannot('do-admin-stuff')
                                <li class="nav-item">
                                    <a href="{{ route('share.index') }}"
                                        class="nav-link {{ Request::is('shared_files', 'share*') ? 'active' : '' }}">
                                        <i class="fas fa-square-share-nodes nav-icon"></i>
                                        <p>Shared Files</p>
                                    </a>
                                </li>
                                @endcannot
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('activity.log') }}"
                                class="nav-link {{ Request::is('activities') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-clock"></i>
                                <p>
                                    Activity log
                                </p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        {{-- Content --}}
        <div class="content-wrapper pt-2 hide-on-load" id="contentWrapper">

            @yield('content')

        </div>
        {{-- Footer --}}
        {{-- <footer class="main-footer">

            <div class="float-right d-none d-sm-inline">
                Digitalized Document Storage System
            </div>

            <strong>Copyright &copy; <a href="https://adminlte.io">PUP Calauan, Laguna Campus</a>.</strong> All rights
            reserved.
        </footer> --}}
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        $(document).ready( function () {
            $('#myTable').DataTable();
            $('#contentWrapper').removeClass('hide-on-load');
        });
    </script>

    @include('sweetalert::alert')

    @yield('scripts')
</body>

</html>