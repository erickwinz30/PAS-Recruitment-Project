@extends('layout.main')

@section('container')
  <div class="pagetitle">
    <h1>Approvals</h1>
    <nav>
      <ol class="breadcrumb">
        {{-- <li class="breadcrumb-item"><a href="/dashboard/admin">Admin</a></li> --}}
        <li class="breadcrumb-item active"><a href="/">Request Approvals</a></li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row justify-content-center">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h5 class="card-title">Daftar Permintaan Persetujuan</h5>
              </div>
            </div>

            <!-- Table with stripped rows -->
            <div class="table table-responsive">
              <table class="table" id="request-approval-table">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>User Request</th>
                    <th>Nama Stock</th>
                    <th>Jumlah Stock</th>
                    <th>Masuk/Keluar</th>
                    <th>Tanggal Request</th>
                    <th>Status Approval</th>
                    @if (auth()->user()->is_admin)
                      <th>Aksi</th>
                    @endif
                  </tr>
                </thead>
                <tbody>
                  @if ($requestApprovals->isNotEmpty())
                    @foreach ($requestApprovals as $request)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $request->user->name }} @if (auth()->user()->id === $request->user->id)
                            (Anda)
                          @endif
                        </td>
                        <td>{{ $request->stock->name }}</td>
                        <td>{{ $request->amount }}</td>
                        <td>
                          @if ($request->is_entry)
                            Barang masuk
                          @else
                            Barang keluar
                          @endif
                        </td>
                        <td class="text-center align-middle" style="padding: 0;">
                          <span
                            style="color:#219653; background-color: #e8f4ed; border-radius: 10px; padding: 3px 5px; display: inline-block; box-sizing: border-box">
                            {{ \Carbon\Carbon::parse($request->created_at)->format('d-m-Y H:i:s') }}
                          </span>
                        </td>
                        @if ($request->status == 'pending')
                          <td class="text-center align-middle">
                            <span
                              style="color:#f2c94c; background-color: #fdf3e0; border-radius: 10px; padding: 3px 5px; display: inline-block; box-sizing: border-box">
                              Pending
                            </span>
                          </td>
                        @elseif($request->status == 'approved')
                          <td class="text-center align-middle">
                            <span
                              style="color:#27ae60; background-color: #e8f4ed; border-radius: 10px; padding: 3px 5px; display: inline-block; box-sizing: border-box">
                              Approved
                            </span>
                          </td>
                        @elseif($request->status == 'rejected')
                          <td class="text-center align-middle">
                            <span
                              style="color:#eb5757; background-color: #fbe9e9; border-radius: 10px; padding: 3px 5px; display: inline-block; box-sizing: border-box">
                              Rejected
                            </span>
                          </td>
                        @endif
                        @if (auth()->user()->is_admin)
                          <td>
                            @if (auth()->user()->is_admin)
                              <button type="button" class="btn btn-success" id="btn-accept-approval"
                                onclick="acceptRequestConfirmation('{{ $request->id }}')">Terima</button>
                              <button type="button" class="btn btn-danger" id="btn-reject-approval">Tolak</button>
                            @endif
                          </td>
                        @endif
                      </tr>
                    @endforeach
                  @else
                    <tr>
                      <td colspan="7" class="text-center">No transactions found for the selected date range.</td>
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

    <!-- Create Modal -->
    {{-- <div class="modal fade" id="addNewStockModal" tabindex="-1" aria-labelledby="newStockLabel" aria-hidden="true">
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
    </div> --}}
  </section>

  <script>
    $(document).ready(function() {
      var table = $('#request-approval-table').DataTable({
        scrollX: true,
        columns: [{
            data: 'no',
            defaultContent: '<i>Not set</i>'
          },
          {
            data: 'user',
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
            data: 'is_entry',
            defaultContent: '<i>Not set</i>'
          },
          {
            data: 'created_at',
            defaultContent: '<i>Not set</i>'
          },
          {
            data: 'status',
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

      // Reset button functionality (updated)
      // $('#tombolReset').on('click', function() {
      //   table.search('').columns().search('').draw();
      // });
    });

    function refreshStockTable() {
      $.ajax({
        url: '/request-approval',
        type: 'GET',
        dataType: 'html',
        success: function(data) {
          // Ambil tbody baru dari response dan replace tbody lama
          var newTbody = $(data).find('#request-approval-table tbody').html();
          $('#request-approval-table tbody').html(newTbody);
        }
      });
    }

    function acceptRequestConfirmation(requestId) {
      Swal.fire({
        title: "Yakin ingin menerima request penambahan stock ini?",
        text: "Aksi ini tidak bisa mengembalikan data ke semula!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#2980B9",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      }).then((result) => {
        if (result.isConfirmed) {
          console.log("Accept confirmed. Trying to add stock data...");
          // document.getElementById('deleteForm' + stockId).submit();
          // $(`#deleteForm${stockId}`).submit();
          submitAcceptRequest(requestId);
        }
      });
    }

    function submitAcceptRequest(requestId) {
      // var form = $(`#deleteForm${stockId}`);
      var url = `/request-approval/accept/${requestId}`;
      // var formData = form.serialize();

      $.ajax({
        url: url,
        type: 'POST',
        // data: formData,
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        success: function(response) {
          console.log("Successfully accepted request approval and add stock:", response);

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

    // function requestApprovalConfirmation() {
    //   Swal.fire({
    //     title: "Yakin ingin melakukan penambahan / pengurangan data stock?",
    //     text: "Aksi ini akan diinformasikan ke admin untuk dilakukan konfirmasi!",
    //     icon: "warning",
    //     showCancelButton: true,
    //     confirmButtonColor: "#2980B9",
    //     cancelButtonColor: "#d33",
    //     confirmButtonText: "Yes, request it!"
    //   }).then((result) => {
    //     if (result.isConfirmed) {
    //       console.log("Requesting Approval Confirmed. Trying to request approval to admin...");
    //       // document.getElementById('editStockForm').submit();
    //       submitRequestApproval();
    //     }
    //   });
    // }
  </script>
@endsection
