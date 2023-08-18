<div class="row mb-4">
    <div class="col-lg-4">
        <table class="table table-bordered" width="100%">
            <tr>
                <th style="text-align: center" colspan="2">
                    <h6>Stok Ayam <br>{{tanggal(date('Y-m-d'))}}</h6>
                </th>
            </tr>
            <tr>
                <th style="text-align: center; height: 60px;">
                    <h6>Stok Martadah <br>{{$stok_ayam->saldo_kandang}}</h6>
                </th>
                <th style="text-align: center; height: 60px;">
                    <h6>Stok BJM <br>{{empty($stok_ayam_bjm->saldo_bjm) ? '0' : $stok_ayam_bjm->saldo_bjm}}</h6>
                </th>
            </tr>
            <tr>
                <th style="text-align: center" colspan="2">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#transfer_ayam"
                        class="btn btn-sm btn-primary">Transfer BJM</a>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#penjualan_ayam"
                        class="btn btn-sm btn-primary">Penjualan Martadah</a>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#history_ayam"
                        class="btn btn-sm btn-primary">History </a>
                </th>
            </tr>

        </table>
    </div>
    <div class="col-lg-4">
        <table class="table table-bordered" width="100%">
            <tr>
                <th style="text-align: center">
                    <h6>Stok Pupuk <br>{{tanggal(date('Y-m-d'))}}</h6>
                </th>
            </tr>
            <tr>
                <th style="text-align: center; height: 60px;">
                    <h6>{{empty($stok_pupuk->saldo_pupuk) ? '0' : number_format($stok_pupuk->saldo_pupuk / 50,0)}}
                        Karung
                    </h6>
                </th>
            </tr>
            {{-- <tr>
                <th style="text-align: center">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#penjualan_pupuk"
                        class="btn btn-sm btn-primary">Penjualan Pupuk</a>
                </th>
            </tr> --}}

        </table>
    </div>
    <div class="col-lg-4">
        <table class="table table-bordered" width="100%">
            <tr>
                <th style="text-align: center">
                    <h6>Stok Karung <br>{{tanggal(date('Y-m-d'))}}</h6>
                </th>
            </tr>
            <tr>
                <th style="text-align: center; height: 60px;">
                    <h6>{{empty($stok_karung->saldo_karung) ? '0' : $stok_karung->saldo_karung}} Karung</h6>
                </th>
            </tr>
            <tr>
                <th style="text-align: center" colspan="2">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#history_karung"
                        class="btn btn-sm btn-primary">History </a>
                </th>
            </tr>

        </table>
    </div>
</div>