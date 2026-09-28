<!DOCTYPE html>
<html dir="ltr" lang="en-US">

<head>
  <title>Fikri Amrullah</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta name="author" content="surfside media" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="shortcut icon" href="{{ asset('logo-fikri.png') }}" type="image/png">
  <link rel="preconnect" href="https://fonts.gstatic.com/">
  <link
    href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap"
    rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Allura&amp;display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/plugins/swiper.min.css') }}" type="text/css" />
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" type="text/css" />
  <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" type="text/css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    integrity="sha512-SfTiTlX6kk+qitfevl/7LibUOeJWlt9rbyDn92a1DqWOw9vWG2MFoays0sgObmWazO5BQPiFucnnEAjpAB+/Sw=="
    crossorigin="anonymous" referrerpolicy="no-referrer">
</head>

<body class="gradient-bg">
  @include('components.customer-navbar')

  @yield('content')

  @include('components.customer-footer')

  <div id="scrollTop" class="visually-hidden end-0"></div>
  <div class="page-overlay"></div>

  <script src="{{ asset('assets/js/plugins/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/bootstrap-slider.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/swiper.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/countdown.js') }}"></script>
  <script src="{{ asset('assets/js/theme.js') }}"></script>

  <script>
    (function() {
      var SearchProto = UomoElements.Search.prototype;

      SearchProto._handleAjaxSearch = UomoHelpers.debounce(function(event, _this) {
        var $form = event.target.closest(_this.selectors.container);
        var keyword = event.target.value.trim();

        if (keyword.length < 2) {
          $form.classList.remove(_this.searchInputFocusedClass);
          return;
        }

        var url = $form.getAttribute('data-search-url');
        if (!url) return;

        fetch(url + '?q=' + encodeURIComponent(keyword), {
            method: 'GET'
          })
          .then(function(r) {
            if (r.ok) return r.json();
            return Promise.reject(r);
          })
          .then(function(data) {
            _this._updateSearchResult(data, $form);
          })
          .catch(function(err) {
            _this._handleAjaxSearchError(err.message, $form);
          });
      }, 180);

      SearchProto._updateSearchResult = function(data, $form) {
        var $rc = $form.querySelector(this.selectors.resultContainer);
        if (!$rc) return;

        if (!data.products || data.products.length === 0) {
          $rc.innerHTML =
            '<div class="search-result__empty text-center py-3 text-secondary">No products found</div>';
          $form.classList.add(this.searchInputFocusedClass);
          return;
        }

        var html = '<div class="search-result__list">';
        data.products.forEach(function(p) {
          html += '<a href="' + p.url +
            '" class="search-result__item d-flex align-items-center gap-3 p-2 text-decoration-none">' +
            '<img src="' + p.first_image + '" alt="' + p.name +
            '" width="50" height="50" class="rounded" style="object-fit:cover;" loading="lazy">' +
            '<div>' +
            '<div class="fw-medium text-dark">' + p.name + '</div>' +
            '<div class="text-secondary small">' + p.price_formatted + '</div>' +
            '</div></a>';
        });
        html += '</div>';

        $rc.innerHTML = html;
        $form.classList.add(this.searchInputFocusedClass);
      };
    })();
  </script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>
