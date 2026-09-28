<div class="header-dashboard">
  <div class="wrap">
    <div class="header-left">
      <a href="{{ asset('admin/index-2.html') }}">
        <img class="" id="logo_header_mobile" alt="" src="{{ asset('logo-fikri.png') }}"
          data-light="{{ asset('logo-fikri.png') }}" data-dark="{{ asset('logo-fikri.png') }}"
          data-retina="{{ asset('logo-fikri.png') }}">
      </a>
      <div class="button-show-hide">
        <i class="icon-menu-left"></i>
      </div>


      <form class="form-search flex-grow">
        <fieldset class="name">
          <input type="text" placeholder="Search here..." class="show-search" name="name" tabindex="2"
            value="" aria-required="true" required="">
        </fieldset>
        <div class="button-submit">
          <button class="" type="submit"><i class="icon-search"></i></button>
        </div>
        <div class="box-content-search" id="box-content-search">
          <ul class="mb-24">
            <li class="mb-14">
              <div class="body-title">Top selling product</div>
            </li>
            <li class="mb-14">
              <div class="divider"></div>
            </li>
            <li>
              <ul>
                <li class="product-item gap14 mb-10">
                  <div class="image no-bg">
                    <img src="{{ asset('admin/images/products/17.png') }}" alt="">
                  </div>
                  <div class="flex items-center justify-between gap20 flex-grow">
                    <div class="name">
                      <a href="{{ asset('admin/product-list.html') }}" class="body-text">Dog Food
                        Rachael Ray Nutrish®</a>
                    </div>
                  </div>
                </li>
                <li class="mb-10">
                  <div class="divider"></div>
                </li>
                <li class="product-item gap14 mb-10">
                  <div class="image no-bg">
                    <img src="{{ asset('admin/images/products/18.png') }}" alt="">
                  </div>
                  <div class="flex items-center justify-between gap20 flex-grow">
                    <div class="name">
                      <a href="{{ asset('admin/product-list.html') }}" class="body-text">Natural
                        Dog Food Healthy Dog Food</a>
                    </div>
                  </div>
                </li>
                <li class="mb-10">
                  <div class="divider"></div>
                </li>
                <li class="product-item gap14">
                  <div class="image no-bg">
                    <img src="{{ asset('admin/images/products/19.png') }}" alt="">
                  </div>
                  <div class="flex items-center justify-between gap20 flex-grow">
                    <div class="name">
                      <a href="{{ asset('admin/product-list.html') }}" class="body-text">Freshpet
                        Healthy Dog Food and Cat</a>
                    </div>
                  </div>
                </li>
              </ul>
            </li>
          </ul>
          <ul class="">
            <li class="mb-14">
              <div class="body-title">Order product</div>
            </li>
            <li class="mb-14">
              <div class="divider"></div>
            </li>
            <li>
              <ul>
                <li class="product-item gap14 mb-10">
                  <div class="image no-bg">
                    <img src="{{ asset('admin/images/products/20.png') }}" alt="">
                  </div>
                  <div class="flex items-center justify-between gap20 flex-grow">
                    <div class="name">
                      <a href="{{ asset('admin/product-list.html') }}" class="body-text">Sojos
                        Crunchy Natural Grain Free...</a>
                    </div>
                  </div>
                </li>
                <li class="mb-10">
                  <div class="divider"></div>
                </li>
                <li class="product-item gap14 mb-10">
                  <div class="image no-bg">
                    <img src="{{ asset('admin/images/products/21.png') }}" alt="">
                  </div>
                  <div class="flex items-center justify-between gap20 flex-grow">
                    <div class="name">
                      <a href="{{ asset('admin/product-list.html') }}" class="body-text">Kristin
                        Watson</a>
                    </div>
                  </div>
                </li>
                <li class="mb-10">
                  <div class="divider"></div>
                </li>
                <li class="product-item gap14 mb-10">
                  <div class="image no-bg">
                    <img src="{{ asset('admin/images/products/22.png') }}" alt="">
                  </div>
                  <div class="flex items-center justify-between gap20 flex-grow">
                    <div class="name">
                      <a href="{{ asset('admin/product-list.html') }}" class="body-text">Mega
                        Pumpkin Bone</a>
                    </div>
                  </div>
                </li>
                <li class="mb-10">
                  <div class="divider"></div>
                </li>
                <li class="product-item gap14">
                  <div class="image no-bg">
                    <img src="{{ asset('admin/images/products/23.png') }}" alt="">
                  </div>
                  <div class="flex items-center justify-between gap20 flex-grow">
                    <div class="name">
                      <a href="{{ asset('admin/product-list.html') }}" class="body-text">Mega
                        Pumpkin Bone</a>
                    </div>
                  </div>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </form>

    </div>
    <div class="header-grid">

      @php
        $notifIcons = [
            'order_placed' => ['item' => 'item-1', 'icon' => 'icon-noti-1'],
            'payment_settlement' => ['item' => 'item-2', 'icon' => 'icon-noti-2'],
            'payment_failed' => ['item' => 'item-3', 'icon' => 'icon-noti-3'],
            'payment_expired' => ['item' => 'item-4', 'icon' => 'icon-noti-4'],
            'low_stock' => ['item' => 'item-1', 'icon' => 'icon-noti-1'],
            'out_of_stock' => ['item' => 'item-3', 'icon' => 'icon-noti-3'],
            'new_review' => ['item' => 'item-2', 'icon' => 'icon-noti-2'],
            'order_cancelled' => ['item' => 'item-4', 'icon' => 'icon-noti-4'],
        ];
      @endphp

      <div class="popup-wrap message type-header">
        <div class="dropdown">
          <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton2"
            data-bs-toggle="dropdown" aria-expanded="false">
            <span class="header-item">
              @if ($unreadCount > 0)
                <span class="text-tiny">{{ $unreadCount }}</span>
              @endif
              <i class="icon-bell"></i>
            </span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end has-content" aria-labelledby="dropdownMenuButton2">
            <li>
              <h6>Notifications</h6>
            </li>
            @forelse ($unreadNotifications as $notification)
              @php
                $style = $notifIcons[$notification->type] ?? ['item' => 'item-1', 'icon' => 'icon-noti-1'];
              @endphp
              <li>
                <a href="#" class="text-decoration-none"
                  onclick="event.preventDefault(); markNotifRead({{ $notification->id }}, this)">
                  <div class="message-item {{ $style['item'] }}">
                    <div class="image">
                      <i class="{{ $style['icon'] }}"></i>
                    </div>
                    <div>
                      <div class="body-title-2">{{ $notification->title }}</div>
                      <div class="text-tiny">{{ $notification->created_at->diffForHumans() }}</div>
                    </div>
                  </div>
                </a>
              </li>
            @empty
              <li>
                <div class="message-item item-1">
                  <div class="image">
                    <i class="icon-noti-1"></i>
                  </div>
                  <div>
                    <div class="body-title-2">No notifications</div>
                    <div class="text-tiny">You're all caught up!</div>
                  </div>
                </div>
              </li>
            @endforelse
            @if ($unreadCount > 0)
              <li>
                <form method="POST" action="{{ route('admin.notifications.readAll') }}">
                  @csrf
                  <button type="submit" class="tf-button w-full">Mark all as read</button>
                </form>
              </li>
            @endif
          </ul>
        </div>
      </div>

      <form id="mark-read-form" method="POST" style="display:none;">
        @csrf
      </form>

      <script>
        function markNotifRead(id, el) {
          var form = document.getElementById('mark-read-form');
          form.action = '/admin/notifications/' + id + '/read';
          var xhr = new XMLHttpRequest();
          xhr.open('POST', form.action, true);
          xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
          xhr.onload = function() {
            if (xhr.status === 200) {
              var li = el.closest('li');
              var dropdown = li.closest('.dropdown-menu');
              li.remove();
              var badge = dropdown.closest('.popup-wrap').querySelector('.header-item .text-tiny');
              if (badge) {
                var count = parseInt(badge.textContent) - 1;
                if (count <= 0) {
                  badge.remove();
                  var markAllBtn = dropdown.querySelector('form button.tf-button');
                  if (markAllBtn) {
                    markAllBtn.closest('li').remove();
                  }
                  var emptyItem = document.createElement('li');
                  emptyItem.innerHTML = '<div class="message-item item-1"><div class="image"><i class="icon-noti-1"></i></div><div><div class="body-title-2">No notifications</div><div class="text-tiny">You\'re all caught up!</div></div></div>';
                  var h6Item = dropdown.querySelector('li h6');
                  if (h6Item) {
                    h6Item.closest('li').insertAdjacentElement('afterend', emptyItem);
                  }
                } else {
                  badge.textContent = count;
                }
              }
            }
          };
          xhr.send();
        }
      </script>

      <div class="popup-wrap user type-header">
        <div class="dropdown">
          <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton3"
            data-bs-toggle="dropdown" aria-expanded="false">
            <span class="header-user wg-user">
              <span class="image">
                <img src="{{ asset('admin/images/avatar/user-1.png') }}" alt="">
              </span>
              <span class="flex flex-column">
                <span class="body-title mb-2">Kristin Watson</span>
                <span class="text-tiny">Admin</span>
              </span>
            </span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end has-content" aria-labelledby="dropdownMenuButton3">
            <li>
              <a href="#" class="user-item">
                <div class="icon">
                  <i class="icon-user"></i>
                </div>
                <div class="body-title-2">Account</div>
              </a>
            </li>
            <li>
              <a href="#" class="user-item">
                <div class="icon">
                  <i class="icon-mail"></i>
                </div>
                <div class="body-title-2">Inbox</div>
                <div class="number">27</div>
              </a>
            </li>
            <li>
              <a href="#" class="user-item">
                <div class="icon">
                  <i class="icon-file-text"></i>
                </div>
                <div class="body-title-2">Taskboard</div>
              </a>
            </li>
            <li>
              <a href="#" class="user-item">
                <div class="icon">
                  <i class="icon-headphones"></i>
                </div>
                <div class="body-title-2">Support</div>
              </a>
            </li>
            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                  class="user-item w-full text-left bg-transparent border-0 p-0 m-0 hover:bg-gray-100 flex items-center">
                  <div class="icon">
                    <i class="icon-log-out"></i>
                  </div>
                  <div class="body-title-2 text-start">Log out</div>
                </button>
              </form>
            </li>
          </ul>
        </div>
      </div>

    </div>
  </div>
</div>
