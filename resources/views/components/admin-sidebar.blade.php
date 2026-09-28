<div class="section-menu-left">
  <div class="box-logo">
    <a href="#" id="site-logo-inner">
      <img class="" id="logo_header" alt="" src="{{ asset('logo-fikri.png') }}"
        data-light="{{ asset('logo-fikri.png') }}" data-dark="{{ asset('logo-fikri.png') }}">
    </a>
    <div class="button-show-hide">
      <i class="icon-menu-left"></i>
    </div>
  </div>
  <div class="center">
    <div class="center-item">
      <div class="center-heading">Main Home</div>
      <ul class="menu-list">
        <li class="menu-item">
          <a href="{{ route('admin.dashboard') }}" class="">
            <div class="icon"><i class="icon-grid"></i></div>
            <div class="text">Dashboard</div>
          </a>
        </li>
      </ul>
    </div>
    <div class="center-item">
      <ul class="menu-list">
        <li class="menu-item @if (request()->routeIs('admin.products.*')) active @endif">
          <a href="{{ route('admin.products.index') }}" class="">
            <div class="icon"><i class="icon-shopping-cart"></i></div>
            <div class="text">Products</div>
          </a>
        </li>
        <li class="menu-item @if (request()->routeIs('admin.brands.*')) active @endif">
          <a href="{{ route('admin.brands.index') }}" class="">
            <div class="icon"><i class="icon-layers"></i></div>
            <div class="text">Brand</div>
          </a>
        </li>
        <li class="menu-item @if (request()->routeIs('admin.categories.*')) active @endif">
          <a href="{{ route('admin.categories.index') }}" class="">
            <div class="icon"><i class="icon-layers"></i></div>
            <div class="text">Category</div>
          </a>
        </li>

        <li class="menu-item @if (request()->routeIs('admin.orders.*')) active @endif">
          <a href="{{ route('admin.orders.index') }}" class="">
            <div class="icon"><i class="icon-file-plus"></i></div>
            <div class="text">Orders</div>
          </a>
        </li>
        <li class="menu-item @if (request()->routeIs('admin.sliders.*')) active @endif">
          <a href="{{ route('admin.sliders.index') }}" class="">
            <div class="icon"><i class="icon-image"></i></div>
            <div class="text">Slider</div>
          </a>
        </li>
        <li class="menu-item @if (request()->routeIs('admin.coupons.*')) active @endif">
          <a href="{{ route('admin.coupons.index') }}" class="">
            <div class="icon"><i class="icon-grid"></i></div>
            <div class="text">Coupons</div>
          </a>
        </li>

        <li class="menu-item @if (request()->routeIs('admin.reviews.*')) active @endif">
          <a href="{{ route('admin.reviews.index') }}" class="">
            <div class="icon"><i class="icon-star"></i></div>
            <div class="text">Reviews</div>
          </a>
        </li>

        <li class="menu-item @if (request()->routeIs('admin.activities.*')) active @endif">
          <a href="{{ route('admin.activities.index') }}" class="">
            <div class="icon"><i class="icon-file-text"></i></div>
            <div class="text">Activity</div>
          </a>
        </li>

        <li class="menu-item @if (request()->routeIs('admin.customers.*')) active @endif">
          <a href="{{ route('admin.customers.index') }}" class="">
            <div class="icon"><i class="icon-user"></i></div>
            <div class="text">User</div>
          </a>
        </li>

        <li class="menu-item @if (request()->routeIs('admin.settings.*')) active @endif">
          <a href="{{ route('admin.settings.index') }}" class="">
            <div class="icon"><i class="icon-settings"></i></div>
            <div class="text">Settings</div>
          </a>
        </li>

        <li class="menu-item">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
              class="flex items-center justify-start gap-2 w-full px-4 py-3 bg-transparent border-0 cursor-pointer">
              <div class="icon"><i class="icon-log-out"></i></div>
              <div class="text">Log Out</div>
            </button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</div>
