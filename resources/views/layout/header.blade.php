<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center justify-content-between">

  <div class="d-flex align-items-center justify-content-between">
    <a href="/stock" class="logo d-flex align-items-center">
      <img src="{{ asset('img/pas_logo.jpg') }}" alt="Kenzou Logo">
      <span class="d-none d-lg-block fs-5">Prakarsa Alam Segar</span>
    </a>
    <i class="bi bi-list toggle-sidebar-btn"></i>
  </div><!-- End Logo -->

  <div>
    <p class="m-0 me-3 fw-semibold" style="color: #012970">{{ auth()->user()->name }}</p>
  </div>

  {{-- <div>
        @if (Request::is('dashboard/transaksi'))
            <div class="me-4">
                <a href="/dashboard/transaksiBaru" class="btn btn-success">
                    <i class="bi bi-plus me-1"></i>Transaksi
                </a>
            </div>
        @elseif (Request::is('dashboard/kasir'))
            <div class="me-4">
                <button type="button" class="btn btn-success d-inline" data-bs-toggle='modal'
                    data-bs-target='#inputModal'>
                    <i class="bi bi-plus" style="margin-right: 2px;"></i>Kasir
                </button>
            </div>
        @elseif (Request::is('dashboard/voucher'))
            <div class="me-4">
                <a href="/dashboard/voucher/create" type="button" class="btn btn-success d-inline">
                    <i class="bi bi-plus me-1"></i>Voucher
                </a>
            </div>
        @elseif (Request::is('dashboard/challenge'))
            <div class="me-4">
                <a href="/dashboard/challenge/create" type="button" class="btn btn-success d-inline">
                    <i class="bi bi-plus me-1"></i>Challenge
                </a>
            </div>
        @endif --}}
  </div>

</header><!-- End Header -->
