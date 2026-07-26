@props(['active' => 'dashboard'])

<div class="col-lg-3">
  <ul class="account-nav">
    <li>
      <a href="{{ route('customer.dashboard') }}" class="menu-link menu-link_us-s {{ $active === 'dashboard' ? 'menu-link_active' : '' }}">Dashboard</a>
    </li>
    <li>
      <a href="{{ route('customer.orders.index') }}" class="menu-link menu-link_us-s {{ $active === 'orders' ? 'menu-link_active' : '' }}">Orders</a>
    </li>
    <li>
      <a href="{{ route('customer.addresses.index') }}" class="menu-link menu-link_us-s {{ $active === 'addresses' ? 'menu-link_active' : '' }}">Addresses</a>
    </li>
    <li>
      <a href="#" class="menu-link menu-link_us-s {{ $active === 'account-details' ? 'menu-link_active' : '' }}">Account Details</a>
    </li>
    <li>
      <a href="#" class="menu-link menu-link_us-s {{ $active === 'wishlist' ? 'menu-link_active' : '' }}">Wishlist</a>
    </li>
    <li>
      <form method="POST" action="{{ route('logout') }}" class="d-inline">
        @csrf
        <button type="submit" class="menu-link menu-link_us-s"
          style="background:none;border:none;padding:0;cursor:pointer;color:inherit;font:inherit">Logout</button>
      </form>
    </li>
  </ul>
</div>
