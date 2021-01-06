<?php

    $routes = array();
    function setActiveMenu($route)
    {   if( $route == 'admin' ) {
          return (Request::is($route) || Request::is($route)) ? 'active' : '';
        }
        return (Request::is($route) || Request::is($route.'/*')) ? 'active' : '';
    }

    function setOpenSubMenu($route)
    {
        return (Request::is($route) || Request::is($route.'/*')) ? 'open' : '';
    }

?>

<nav class="sidebar sidebar-collapsed">
    <div class="sidebar-content ">
        <a class="sidebar-brand" href="{{ url("admin") }}">
           <img src="{{ asset('admin-theme/images/wag-header-bar-logo-white.svg') }}" alt="Wag Enabled" />
            {{-- Wag Enabled --}}
        </a>
        <ul class="sidebar-nav">

            <li class="sidebar-item {{ setActiveMenu('admin') }}">
                <a class="sidebar-link" href="{{ url('admin') }}">
                    <span class="align-middle">Dashboard</span>
                </a>
            </li>

            <li class="sidebar-item {{ setActiveMenu('admin/users') }}">
                <a class="sidebar-link" href="{{ url('admin/users') }}">
                    <span class="align-middle">Users</span>
                </a>
            </li>

            <li class="sidebar-item {{ setActiveMenu('admin/pet-pro-categories') }}">
                <a class="sidebar-link" href="{{ url('admin/pet-pro-categories') }}">
                    <span class="align-middle">Pet Pro Categories</span>
                </a>
            </li>

            <li class="sidebar-item {{ setActiveMenu('admin/pet-pros') }}">
                <a class="sidebar-link" href="{{ url('admin/pet-pros') }}">
                    <span class="align-middle">Pet Pros</span>
                </a>
            </li>

            <li class="sidebar-item {{ setActiveMenu('admin/product-review-categories') }}">
                <a class="sidebar-link" href="{{ url('admin/product-review-categories') }}">
                    <span class="align-middle">Product Review Categories</span>
                </a>
            </li>

            <li class="sidebar-item {{ setActiveMenu('admin/product-reviews') }}">
                <a class="sidebar-link" href="{{ url('admin/product-reviews') }}">
                    <span class="align-middle">Product Reviews</span>
                </a>
            </li>

            <li class="sidebar-item {{ setActiveMenu('admin/watch-and-learn-categories') }}">
                <a class="sidebar-link" href="{{ url('admin/watch-and-learn-categories') }}">
                    <span class="align-middle">Watch And Learn Categories</span>
                </a>
            </li>            

            <li class="sidebar-item {{ setActiveMenu('admin/watch-and-learn') }}">
                <a class="sidebar-link" href="{{ url('admin/watch-and-learn') }}">
                    <span class="align-middle">Watch And Learn</span>
                </a>
            </li>

            <li class="sidebar-item {{ setActiveMenu('admin/watch-and-learn-author') }}">
                <a class="sidebar-link" href="{{ url('admin/watch-and-learn-author') }}">
                    <span class="align-middle">Authors</span>
                </a>
            </li>

            <li class="sidebar-item {{ setActiveMenu('admin/watch-and-learn-medias') }}">
                <a class="sidebar-link" href="{{ url('admin/watch-and-learn-medias') }}">
                    <span class="align-middle">Medias</span>
                </a>
            </li>

            <li class="sidebar-item {{ setActiveMenu('admin/contacts') }}">
                <a class="sidebar-link" href="{{ url('admin/contacts') }}">
                    <span class="align-middle">Contacts</span>
                </a>
            </li>

            <li class="sidebar-item {{ setActiveMenu('admin/business-requests') }}">
                <a class="sidebar-link" href="{{ url('admin/business-requests') }}">
                    <span class="align-middle">Business Requests</span>
                </a>
            </li>

            <li class="sidebar-item {{ setActiveMenu('admin/newsletters') }}">
                <a class="sidebar-link" href="{{ url('admin/newsletters') }}">
                    <span class="align-middle">Newsletters</span>
                </a>
            </li>

            <li class="sidebar-item {{ setActiveMenu('admin/admin-users') }}">
                <a class="sidebar-link" href="{{ url('admin/admin-users') }}">
                    <span class="align-middle">Admin Users</span>
                </a>
            </li>

            <li class="sidebar-item {{ setActiveMenu('admin/testimonial') }}">
                <a class="sidebar-link" href="{{ url('admin/testimonial') }}">
                    <span class="align-middle">Testimonial</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link" href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('topbar-logout-form').submit();">Sign out</a>
                <form id="topbar-logout-form" action="{{ url('admin/logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </li>
        </ul>
    </div>
</nav>