{{-- modal --}}
<form action="{{route('dashboard_kandang.penjualan_ayam')}}" method="post">
    @csrf
    <x-theme.modal title="Penjualan ayam" idModal="penjualan_ayam">
        <div class="row">
            <div class="col-lg-4">
                <label for="">Tanggal</label>
                <input type="date" class="form-control" value="{{date('Y-m-d')}}" name="tgl">
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
                <input type="number" min="0" max="{{$stok_ayam->saldo_kandang}}" class="form-control ekor" name="qty"
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

<x-theme.modal title="Penjualan ayam" idModal="history_ayam">
    <div class="row">
        <div class="col-lg-4">
            <label for="">Tanggal</label>
            <input type="date" class="form-control" value="{{date('Y-m-d')}}" name="tgl">
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
            <input type="number" min="0" max="{{$stok_ayam->saldo_kandang}}" class="form-control ekor" name="qty"
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
{{-- end modal --}}