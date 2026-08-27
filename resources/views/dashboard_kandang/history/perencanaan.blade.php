<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <h5 class="mb-0">{{ $title }}</h5>
    </x-slot>

    <x-slot name="cardBody">
        <div class="card">
            <div class="card-body">
                <form id="filterPerencanaan" class="row align-items-end g-3">
                    <div class="col-lg-3 col-md-5">
                        <label for="tglHistoryPerencanaan" class="form-label">Tanggal</label>
                        <input value="{{ date('Y-m-d') }}" required type="date"
                            id="tglHistoryPerencanaan" class="form-control">
                    </div>
                    <div class="col-lg-4 col-md-5">
                        <label for="idKandangPerencanaan" class="form-label">Kandang</label>
                        <select required class="form-control select2" id="idKandangPerencanaan">
                            <option value="">Pilih kandang</option>
                            @foreach ($kandang as $d)
                                <option value="{{ $d->id_kandang }}">
                                    {{ $d->nm_kandang }}{{ $d->selesai == 'Y' ? ' (selesai)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-2">
                        <button type="submit" class="btn btn-primary" id="btnPerencanaan">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="hasilPerencanaan" class="mt-3"></div>
    </x-slot>

    @section('js')
        <script>
            $(document).ready(function() {
                $('.select2').select2();

                $('#filterPerencanaan').on('submit', function(e) {
                    e.preventDefault();

                    const tombol = $('#btnPerencanaan');
                    tombol.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memuat');

                    $.ajax({
                        type: 'GET',
                        url: "{{ route('dashboard_kandang.viewHistoryPerencanaan') }}",
                        data: {
                            tgl: $('#tglHistoryPerencanaan').val(),
                            id_kandang: $('#idKandangPerencanaan').val(),
                            history_page: 1
                        },
                        success: function(response) {
                            $('#hasilPerencanaan').html(response);
                        },
                        error: function(xhr) {
                            const pesan = xhr.responseJSON?.message || 'History perencanaan gagal dimuat.';
                            $('#hasilPerencanaan').html(
                                $('<div>').addClass('alert alert-danger').text(pesan)
                            );
                        },
                        complete: function() {
                            tombol.prop('disabled', false)
                                .html('<i class="fas fa-search"></i> Tampilkan');
                        }
                    });
                });
            });
        </script>
    @endsection
</x-theme.app>
