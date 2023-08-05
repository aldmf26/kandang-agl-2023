{{-- modal --}}
<form action="{{route('dashboard_kandang.transfer_ayam')}}" method="post">
    @csrf
    <x-theme.modal title="Transfer ayam" idModal="transfer_ayam">
        <div class="row">
            <div class="col-lg-6">
                <label for="">Tanggal</label>
                <input type="date" name="tgl" class="form-control" value="{{date('Y-m-d')}}" readonly>
            </div>
            <div class="col-lg-6">
                <label for="">Qty</label>
                <input type="text" name="qty" class="form-control" value="0">
            </div>
        </div>
    </x-theme.modal>
</form>
{{-- end modal --}}