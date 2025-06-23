@extends('layout.main')

@section('container')
  <div class="pagetitle">
    <h1>Stock</h1>
    <nav>
      <ol class="breadcrumb">
        {{-- <li class="breadcrumb-item"><a href="/dashboard/admin">Admin</a></li> --}}
        <li class="breadcrumb-item active"><a href="/stock">Stock</a></li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  {{-- @if (session()->has('success'))
    <x-alert-success :message="session('success')" />
  @endif
  <div class="row justify-content-center">
    <div class="alert alert-success alert-dismissible fade show col-lg-12" role="alert">
      {{ $message }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div> --}}

  {{-- @if (session()->has('error'))
    <x-alert-error :message="session('error')" />
  <div class="row justify-content-center">
    <div class="alert alert-danger alert-dismissible fade show col-lg-12 justify-content-center" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
  @endif --}}


  <section class="section">
    <div class="row justify-content-center">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h5 class="card-title">Stock</h5>
              </div>
              <div>
                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                  data-bs-target="#addNewStockModal">Baru</button>
              </div>
            </div>

            <!-- Table with stripped rows -->
            <div class="table table-responsive">
              <table class="table" id="stock-table">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Jumlah Stock</th>
                    <th>Terakhir Masuk</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @if ($stocks->isNotEmpty())
                    @foreach ($stocks as $stock)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $stock->name }}</td>
                        <td>{{ $stock->amount }}</td>
                        <td class="text-center align-middle" style="padding: 0;">
                          <span
                            style="color:#219653; background-color: #e8f4ed; border-radius: 10px; padding: 3px 5px; display: inline-block; box-sizing: border-box">
                            {{ \Carbon\Carbon::parse($stock->updated_at)->format('d-m-Y H:i:s') }}
                          </span>
                        </td>
                        <td>
                          {{-- <a href="/transaksi/{{ $transaksi->id }}" class="btn btn-info"><i class="bi bi-eye"></i></a> --}}
                          <button type="button" class="btn btn-warning" id="btn-request-approval"
                            data-stock-id="{{ $stock->id }}" data-bs-toggle="modal"
                            data-bs-target="#requestApprovalModal">Masuk/Keluar</button>
                          @if (auth()->user()->is_admin)
                            <button type="button" class="btn btn-warning" id="btn-edit"
                              data-edit-id="{{ $stock->id }}" data-bs-toggle="modal"
                              data-bs-target="#editStockModal"><i class="bi bi-pencil"></i></button>
                            <form action="/stock/{{ $stock->id }}" method="POST" class="d-inline delete-form"
                              id="deleteForm{{ $stock->id }}">
                              @method('DELETE')
                              @csrf
                              <button type="button" class="btn btn-danger"
                                onclick="deleteConfirmation('{{ $stock->id }}')">
                                <i class="bi bi-trash"></i>
                              </button>
                            </form>
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  @else
                    <tr>
                      <td colspan="5" class="text-center">No transactions found for the selected date range.</td>
                    </tr>
                  @endif
                </tbody>
                {{-- <tfoot>
                  <tr>
                    <th colspan="10" class="text-right">Total Penjualan:</th>
                    <th id="totalHarga">Rp 0</th>
                    <th colspan="1"></th>
                  </tr>
                </tfoot> --}}
              </table>
            </div>
            <!-- End Table with stripped rows -->
          </div>
        </div>
      </div>
    </div>

    @if (auth()->user()->is_admin)
      <!-- Create Modal -->
      <div class="modal fade" id="addNewStockModal" tabindex="-1" aria-labelledby="newStockLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Barang Baru</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/stock" method="post" id="addStockForm">
              <div class="modal-body">
                <div class="mb-3">
                  <label for="name" class="form-label @error('name') is-invalid @enderror">Nama
                    Barang Baru</label>
                  <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"
                    required autofocus>
                  @error('name')
                    <div class="invalid-feedback">
                      {{ $message }}
                    </div>
                  @enderror
                </div>
                <div class="mb-3">
                  <label for="amount" class="form-label">Jumlah Barang</label>
                  <input type="text" inputmode="numeric" class="form-control" id="amount" name="amount"
                    value="{{ old('amount') }}" required autofocus>
                  @error('amount')
                    <div class="invalid-feedback">
                      {{ $message }}
                    </div>
                  @enderror
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    @endif

    @if (auth()->user()->is_admin)
      <!-- Edit Modal -->
      <div class="modal fade" id="editStockModal" tabindex="-1" aria-labelledby="editStockLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Stock Barang</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/stock/" method="post" id="editStockForm">
              @csrf
              @method('PUT')
              <div class="modal-body">
                <input type="text" class="form-control" id="edit-id" name="id" required hidden>
                <div class="mb-3">
                  <label for="name" class="form-label @error('name') is-invalid @enderror">Nama
                    Barang</label>
                  <input type="text" class="form-control" id="edit-name" name="name" required>
                  @error('name')
                    <div class="invalid-feedback">
                      {{ $message }}
                    </div>
                  @enderror
                </div>
                <div class="mb-3">
                  <label for="edit-amount" class="form-label @error('amount') is-invalid @enderror">Jumlah
                    Barang</label>
                  <input type="text" inputmode="numeric" class="form-control" id="edit-amount" name="amount"
                    required>
                  @error('name')
                    <div class="invalid-feedback">
                      {{ $message }}
                    </div>
                  @enderror
                </div>
                {{-- <div class="mb-3">
                <h6 class="card-text fw-semibold mt-3">Masuk/Keluar</h6>
                <div class="d-flex justify-content-evenly align-items-center">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="is_entry" id="is_admin_1" value="1" />
                    <label class="form-check-label" for="is_admin_1">
                      Masuk </label>
                  </div>
                  <div class="form-check ms-3">
                    <input class="form-check-input" type="radio" name="is_entry" id="is_admin_2" value="0" />
                    <label class="form-check-label" for="is_admin_2">
                      Keluar </label>
                  </div>
                </div>
              </div> --}}
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn-update-"
                  onclick="editConfirmation()">Update</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    @endif


    <!-- Request Modal -->
    <div class="modal fade" id="requestApprovalModal" tabindex="-1" aria-labelledby="requestApprovalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="requestApprovalLabel">Barang Masuk / Keluar</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="/requestApproval" method="post" id="requestApprovalForm">
            @csrf
            <div class="modal-body">
              <input type="text" class="form-control" id="request-approval-id" name="stock_id" required hidden>
              <div class="mb-3">
                <label for="request-approval-name"
                  class="form-label @error('request-approval-name') is-invalid @enderror">Nama
                  Barang</label>
                <input type="text" class="form-control" id="request-approval-name" name="name" disabled
                  required>
                @error('request-approval-name')
                  <div class="invalid-feedback">
                    {{ $message }}
                  </div>
                @enderror
              </div>
              <div class="mb-3">
                <label for="request-approval-amount" class="form-label @error('amount') is-invalid @enderror">Jumlah
                  Barang</label>
                <input type="text" inputmode="numeric" class="form-control" id="request-approval-amount"
                  name="amount" required>
                @error('name')
                  <div class="invalid-feedback">
                    {{ $message }}
                  </div>
                @enderror
              </div>
              <div class="mb-3">
                <h6 class="card-text fw-semibold mt-3">Barang Masuk/Keluar</h6>
                <div class="d-flex justify-content-evenly align-items-center">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="is_entry" id="request-approval-entry"
                      value="1" />
                    <label class="form-check-label" for="request-approval-entry">
                      Masuk </label>
                  </div>
                  <div class="form-check ms-3">
                    <input class="form-check-input" type="radio" name="is_entry" id="request-approval-outgoing"
                      value="0" />
                    <label class="form-check-label" for="request-approval-outgoing">
                      Keluar </label>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" id="btn-request-approval-"
                onclick="requestApprovalConfirmation()">Request</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

  <script>
    $(document).ready(function() {
      var table = $('#stock-table').DataTable({
        scrollX: true,
        columns: [{
            data: 'no',
            defaultContent: '<i>Not set</i>'
          },
          {
            data: 'name',
            defaultContent: '<i>Not set</i>'
          },
          {
            data: 'amount',
            defaultContent: '<i>Not set</i>'
          },
          {
            data: 'created_at',
            defaultContent: '<i>Not set</i>'
          },
          {
            data: 'action',
            defaultContent: '<i>Not set</i>'
          },
        ],
        dom: 'Bfrtip', // Menambahkan tombol ekspor ke DOM
        buttons: [
          'csv', 'excel', 'pdf', 'print'
        ]

      });

      $('#addStockForm').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var url = form.attr('action');
        var formData = form.serialize();

        $.ajax({
          url: url,
          type: 'POST',
          data: formData,
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          success: function(response) {
            $('#addNewStockModal').modal('hide');
            $('#addStockForm')[0].reset();

            setTimeout(function() {
              $('.modal-backdrop').remove();
              $('body').removeClass('modal-open');
              $('body').css({
                'overflow': '',
                'padding-right': ''
              });
              $('#addNewStockModal').removeClass('show').attr('aria-modal', null).css('display',
                'none');
            }, 500);

            $('#dynamic-alert').remove();

            var alertHtml = `
              <div class="row justify-content-center" id="dynamic-alert">
                <div class="alert alert-success alert-dismissible fade show col-lg-12 justify-content-center" role="alert">
                  ${response.message}
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              </div>
            `;

            // $('#dynamic-alert').remove();
            $('.pagetitle').after(alertHtml);

            // Refresh tabel stock (ambil ulang data via AJAX)
            refreshStockTable();
          },
          error: function(xhr) {
            // Hapus alert error sebelumnya jika ada
            $('#dynamic-alert').remove();

            // Ambil pesan error dari response
            var message = 'Terjadi kesalahan.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
              message = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
              message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
            }

            var alertHtml = `
              <div class="row justify-content-center" id="dynamic-alert">
                <div class="alert alert-danger alert-dismissible fade show col-lg-12 justify-content-center" role="alert">
                  ${message}
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              </div>
            `;
            $('.pagetitle').after(alertHtml);

            var errors = xhr.responseJSON && xhr.responseJSON.errors ? xhr.responseJSON.errors : null;
            if (errors) {
              if (errors.name) {
                $('#name').addClass('is-invalid');
                $('#name').next('.invalid-feedback').text(errors.name[0]);
              }
              if (errors.amount) {
                $('#amount').addClass('is-invalid');
                $('#amount').next('.invalid-feedback').text(errors.amount[0]);
              }
            }
          }
        });
      });

      $(document).on('click', '#btn-edit', function() {
        var stockId = $(this).data('edit-id');

        // Ambil data stock via AJAX
        $.ajax({
          url: '/stock/' + stockId + '/edit',
          type: 'GET',
          success: function(data) {
            $('#edit-id').val(data.id);
            $('#editStockForm').attr('action', '/stock/' + data.id);
            $('#editStockForm input[name="id"]').val(data.id);
            $('#editStockForm input[name="name"]').val(data.name);
            $('#editStockForm input[name="amount"]').val(data.amount);
            $('#btn-update-').attr('id', 'btn-update-' + data.id);

            // Tampilkan modal edit
            $('#editStockModal').modal('show');
          },
          error: function(xhr) {
            alert('Gagal mengambil data stock.');
          }
        });
      });

      $(document).on('click', '#btn-request-approval', function() {
        var stockId = $(this).data('stock-id');
        console.log("Request Approval for stock ID: " + stockId);

        // Ambil data stock via AJAX
        $.ajax({
          url: '/getRequestData',
          type: 'GET',
          data: {
            id: stockId
          },
          success: function(data) {
            // $('#request-approval-id').val(data.id);
            // $('#request-approval-id').attr('action', '/stock/' + data.id);
            $('#requestApprovalForm input[name="stock_id"]').val(data.id);
            $('#requestApprovalForm input[name="name"]').val(data.name);
            // $('#requestApprovalForm input[name="amount"]').val(data.amount);
            $('#btn-request-approval-').attr('id', 'btn-request-approval-' + data.id);

            // Tampilkan modal edit
            $('#requestApprovalModal').modal('show');
            console.log("Data received for request approval:", data);

            console.log("ID:", $('#request-approval-id').val());
            console.log("Name:", $('#request-approval-name').val());
          },
          error: function(xhr) {
            alert('Gagal mengambil data stock.');
          }
        });
      });

      // Reset button functionality (updated)
      // $('#tombolReset').on('click', function() {
      //   table.search('').columns().search('').draw();
      // });
    });

    function refreshStockTable() {
      $.ajax({
        url: '/stock', // Pastikan route ini mengembalikan partial view atau JSON data
        type: 'GET',
        dataType: 'html',
        success: function(data) {
          // Ambil tbody baru dari response dan replace tbody lama
          var newTbody = $(data).find('#stock-table tbody').html();
          $('#stock-table tbody').html(newTbody);
        }
      });
    }

    function submitEditForm() {
      var form = $('#editStockForm');
      var url = form.attr('action');
      var formData = form.serialize();

      $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        // success: function(response) {
        //   console.log("success")
        // },
        // error: function(xhr) {
        //   console.log("error")
        // }
        success: function(response) {
          $('#editStockModal').modal('hide');
          $('#editStockForm')[0].reset();

          setTimeout(function() {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css({
              'overflow': '',
              'padding-right': ''
            });
            $('#editStockModal').removeClass('show').attr('aria-modal', null).css('display',
              'none');
          }, 500);

          $('#dynamic-alert').remove();

          var alertHtml = `
            <div class="row justify-content-center" id="dynamic-alert">
              <div class="alert alert-success alert-dismissible fade show col-lg-12 justify-content-center" role="alert">
                ${response.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            </div>
          `;

          // $('#dynamic-alert').remove();
          $('.pagetitle').after(alertHtml);

          refreshStockTable();
        },
        error: function(xhr) {
          // Hapus alert error sebelumnya jika ada
          $('#dynamic-alert').remove();

          // Ambil pesan error dari response
          var message = 'Terjadi kesalahan.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            message = xhr.responseJSON.message;
          } else if (xhr.responseJSON && xhr.responseJSON.errors) {
            message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
          }

          var alertHtml = `
            <div class="row justify-content-center" id="dynamic-alert">
              <div class="alert alert-danger alert-dismissible fade show col-lg-12 justify-content-center" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            </div>
          `;
          $('.pagetitle').after(alertHtml);

          var errors = xhr.responseJSON && xhr.responseJSON.errors ? xhr.responseJSON.errors : null;
          if (errors) {
            if (errors.name) {
              $('#name').addClass('is-invalid');
              $('#name').next('.invalid-feedback').text(errors.name[0]);
            }
            if (errors.amount) {
              $('#amount').addClass('is-invalid');
              $('#amount').next('.invalid-feedback').text(errors.amount[0]);
            }
          }
        }
      });
    };

    function editConfirmation() {
      Swal.fire({
        title: "Yakin ingin meng-edit data stock?",
        text: "Aksi ini tidak bisa mengembalikan data!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#2980B9",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, update it!"
      }).then((result) => {
        if (result.isConfirmed) {
          console.log("Edit Confirmed. Trying to edit stock data...");
          // document.getElementById('editStockForm').submit();
          submitEditForm();
        }
      });
    }

    function deleteConfirmation(stockId) {
      Swal.fire({
        title: "Yakin ingin menghapus data stock?",
        text: "Aksi ini tidak bisa mengembalikan data!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#2980B9",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      }).then((result) => {
        if (result.isConfirmed) {
          console.log("Delete Confirmed. Trying to delete stock data...");
          // document.getElementById('deleteForm' + stockId).submit();
          // $(`#deleteForm${stockId}`).submit();
          submitDeleteForm(stockId);
        }
      });
    }

    function submitDeleteForm(stockId) {
      var form = $(`#deleteForm${stockId}`);
      var url = form.attr('action');
      var formData = form.serialize();

      $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        success: function(response) {
          console.log("Delete stock data demo success")

          $('#dynamic-alert').remove();

          var alertHtml = `
          <div class="row justify-content-center" id="dynamic-alert">
            <div class="alert alert-success alert-dismissible fade show col-lg-12 justify-content-center" role="alert">
              ${response.message}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          </div>
        `;

          // $('#dynamic-alert').remove();
          $('.pagetitle').after(alertHtml);

          // Refresh tabel stock (ambil ulang data via AJAX)
          refreshStockTable();
        },
        error: function(xhr) {
          // Hapus alert error sebelumnya jika ada
          $('#dynamic-alert').remove();

          // Ambil pesan error dari response
          var message = 'Terjadi kesalahan.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            message = xhr.responseJSON.message;
          } else if (xhr.responseJSON && xhr.responseJSON.errors) {
            message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
          }

          var alertHtml = `
            <div class="row justify-content-center" id="dynamic-alert">
              <div class="alert alert-danger alert-dismissible fade show col-lg-12 justify-content-center" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            </div>
          `;
          $('.pagetitle').after(alertHtml);

          var errors = xhr.responseJSON && xhr.responseJSON.errors ? xhr.responseJSON.errors : null;
          if (errors) {
            if (errors.name) {
              $('#name').addClass('is-invalid');
              $('#name').next('.invalid-feedback').text(errors.name[0]);
            }
            if (errors.amount) {
              $('#amount').addClass('is-invalid');
              $('#amount').next('.invalid-feedback').text(errors.amount[0]);
            }
          }
        }
      });
    };

    function requestApprovalConfirmation() {
      Swal.fire({
        title: "Yakin ingin melakukan penambahan / pengurangan data stock?",
        text: "Aksi ini akan diinformasikan ke admin untuk dilakukan konfirmasi!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#2980B9",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, request it!"
      }).then((result) => {
        if (result.isConfirmed) {
          console.log("Requesting Approval Confirmed. Trying to request approval to admin...");
          // document.getElementById('editStockForm').submit();
          submitRequestApproval();
        }
      });
    }

    function submitRequestApproval() {
      var form = $(`#requestApprovalForm`);
      var url = form.attr('action');
      var formData = form.serialize();

      $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        success: function(response) {
          console.log("Request Approval success");
          $('#requestApprovalModal').modal('hide');
          $('#requestApprovalForm')[0].reset();

          setTimeout(function() {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css({
              'overflow': '',
              'padding-right': ''
            });
            $('#requestApprovalModal').removeClass('show').attr('aria-modal', null).css('display',
              'none');
          }, 500);

          $('#dynamic-alert').remove();

          var alertHtml = `
          <div class="row justify-content-center" id="dynamic-alert">
            <div class="alert alert-success alert-dismissible fade show col-lg-12 justify-content-center" role="alert">
              ${response.message}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          </div>
        `;

          // $('#dynamic-alert').remove();
          $('.pagetitle').after(alertHtml);

          refreshStockTable();
        },
        error: function(xhr) {
          // Hapus alert error sebelumnya jika ada
          $('#dynamic-alert').remove();

          // Ambil pesan error dari response
          var message = 'Terjadi kesalahan.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            message = xhr.responseJSON.message;
          } else if (xhr.responseJSON && xhr.responseJSON.errors) {
            message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
          }

          var alertHtml = `
            <div class="row justify-content-center" id="dynamic-alert">
              <div class="alert alert-danger alert-dismissible fade show col-lg-12 justify-content-center" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            </div>
          `;
          $('.pagetitle').after(alertHtml);

          var errors = xhr.responseJSON && xhr.responseJSON.errors ? xhr.responseJSON.errors : null;
          if (errors) {
            if (errors.name) {
              $('#name').addClass('is-invalid');
              $('#name').next('.invalid-feedback').text(errors.name[0]);
            }
            if (errors.amount) {
              $('#amount').addClass('is-invalid');
              $('#amount').next('.invalid-feedback').text(errors.amount[0]);
            }
          }
        }
      });
    };
  </script>
@endsection
