{{-- modal --}}
<form action="{{ route('dashboard_kandang.penjualan_ayam') }}" method="post">
    @csrf
    <x-theme.modal title="Penjualan ayam" idModal="penjualan_ayam">
        <div class="row">
            <div class="col-lg-4">
                <label for="">Tanggal</label>
                <input type="date" class="form-control" value="{{ date('Y-m-d') }}" name="tgl">
            </div>
            <div class="col-lg-4">
                <label for="">Customer</label>
                <input type="text" class="form-control" name="customer">
            </div>
            <div class="col-lg-12">
                <hr>
            </div>
            <div class="col-lg-4">
                <label for="">Ekor</label>
                <input type="number" min="0" max="{{ $stok_ayam->saldo_kandang }}" class="form-control ekor" name="qty"
                    value="0">
            </div>
            <div class="col-lg-4">
                <label for="">Harga Satuan</label>
                <input type="text" class="form-control h_satuan" name="h_satuan" value="0">
            </div>
            <div class="col-lg-4">
                <label for="">Total Rp</label>
                <input type="text" class="form-control ttl_rp" name="ttl_rp" readonly>
            </div>
        </div>
    </x-theme.modal>
</form>

<x-theme.modal title="History ayam martadah" idModal="history_ayam" btn-save="T">
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered" id="table" width="100%">
                <thead>
                    <th>No</th>
                    <th>Tgl</th>
                    <th>Ket</th>
                    <th class="text-end">Stk Masuk</th>
                    <th class="text-end">Stk Keluar</th>
                    <th class="text-end">Saldo</th>
                </thead>
                <tbody>
                    @php
                    $saldo = 0;
                    @endphp
                    @foreach ($history_ayam as $no => $h)
                    @php
                    $saldo += $h->debit - $h->kredit;
                    @endphp
                    <tr>
                        <td>{{ $no + 1 }}</td>
                        <td style="white-space: nowrap"><a href="#" class="detailPop" tgl="{{ $h->tgl }}">{{ tanggal($h->tgl) }}</a></td>
                        <td>{{ $h->kredit == 0 ? 'Ayam Masuk' : ($h->no_nota != '' ? 'Penjualan' : 'Transfer') }}
                        <td align="right">{{ $h->debit }}</td>
                        <td align="right">{{ $h->kredit }}</td>
                        <td align="right">{{ $saldo }}</td>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-theme.modal>
<x-theme.modal title="History ayam martadah" idModal="history_karung" btn-save="T">
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered" id="table" width="100%">
                <thead>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th class="text-end">Debit</th>
                    <th class="text-end">Kredit</th>
                    <th class="text-end">Saldo</th>
                    <th>Keterangan</th>
                </thead>
                <tbody>
                    @php
                    $saldo = 0;
                    @endphp
                    @foreach ($history_karung as $no => $h)
                    @php
                    $saldo += $h->debit - $h->kredit;
                    @endphp
                    <tr>
                        <td>{{ $no + 1 }}</td>
                        <td style="white-space: nowrap">{{ tanggal($h->tgl) }}</td>
                        <td align="right">{{ $h->debit }}</td>
                        <td align="right">{{ $h->kredit }}</td>
                        <td align="right">{{ $saldo }}</td>
                        <td>{{ $h->kredit == 0 ? 'Karung Masuk' : ($h->no_nota != '' ? 'Penjualan' : 'Transfer') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @section('scripts')
        <script>
            $(document).on('click', '.detailPop', function(e){
                e.preventDefault();
                const tgl 
            })
        </script>
    @endsection
</x-theme.modal>
{{-- end modal --}}