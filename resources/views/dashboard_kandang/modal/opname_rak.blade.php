{{-- modal --}}
<div x-data="{}">

    <form action="{{ route('rak.opname') }}" method="post">
        @csrf
        <x-theme.modal title="Opname Rak Telur" idModal="opname_rak" size="modal-lg">
            <table class="table">
                <tr>
                    <th class="dhead">Tanggal</th>
                    <th class="dhead text-end">Stok Program</th>
                    <th class="dhead text-end">Stok Aktual</th>
                    <th class="dhead text-end">Selisih</th>
                </tr>
                <tr>
                    <td>
                        <input type="date" name="tgl" class="form-control" value="{{ date('Y-m-d') }}"
                            name="tgl">
                    </td>
                    <td>
                        <input x-mask:dynamic="$money($input)" readonly value="{{ $stok_rak->saldo }}" type="text"
                            name="stok_program" class="form-control text-end stokProgramRak">
                    </td>
                    <td>
                        <input x-mask:dynamic="$money($input)" required type="text" name="stok_aktual"
                            class="form-control text-end stokAktualRak">
                    </td>
                    <td>
                        <input readonly value="0" type="text" name="selisih"
                            class="form-control text-end stokSelisihRak">

                    </td>
                </tr>
            </table>
        </x-theme.modal>
    </form>

    <form action="{{ route('rak.create') }}" method="post">
        @csrf
        <x-theme.modal title="Tambah Rak Telur" idModal="tambah_rak" size="modal-lg">
            <table class="table">
                <tr>
                    <th class="dhead">Tanggal</th>
                    <th class="dhead text-end">Qty (1 Ball = 70pcs Jordan)</th>
                    <th class="dhead text-end">Ttl Rp</th>
                    <th class="dhead text-end">Biaya lain-lain</th>
                </tr>

                <tr>
                    <td>
                        <input type="date" name="tgl" class="form-control" value="{{ date('Y-m-d') }}"
                            name="tgl">
                    </td>
                    <td>
                        <input x-mask:dynamic="$money($input)" type="text" name="debit"
                            class="form-control text-end">
                    </td>
                    <td>
                        <input x-mask:dynamic="$money($input)" value="0" required type="text" name="total_rp"
                            class="form-control text-end">

                    </td>
                    <td>
                        <input x-mask:dynamic="$money($input)" value="0" type="text" name="biaya_dll"
                            class="form-control text-end">

                    </td>
                </tr>
            </table>
        </x-theme.modal>

    </form>
    <x-theme.modal title="History Rak Telur" idModal="history_rak" size="modal-lg">
        <div class="card-body">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home" role="tab"
                        aria-controls="home" aria-selected="true">Stok Masuk</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab"
                        aria-controls="profile" aria-selected="false" tabindex="-1">Opname</a>
                </li>

            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade active show" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <h6 class="mt-3">History Masuk Rak Telur</h6>
                    <table class="table">
                        <tr>
                            <th class="dhead">No</th>
                            <th class="dhead">Tgl</th>
                            <th class="dhead text-end">Qty</th>
                            <th class="dhead text-end">Ttl Rp</th>
                            <th class="dhead text-end">Biaya Lain-lain</th>
                            <th class="dhead">Admin</th>
                        </tr>
                        @php
                            $masuk = DB::select("SELECT * FROM `tb_rak_telur` as a
                            WHERE a.id_gudang = 1 AND a.no_nota LIKE '%RAKMSK%'");
                        @endphp
                        @foreach ($masuk as $no => $d)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ tanggal($d->tgl) }}</td>
                                <td class="text-end">{{ number_format($d->debit, 0) }}</td>
                                <td class="text-end">{{ number_format($d->total_rp, 0) }}</td>
                                <td class="text-end">{{ number_format($d->biaya_dll, 0) }}</td>
                                <td>{{ $d->admin }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <h6 class="mt-3">History Opname Rak Telur</h6>
                    <table class="table">
                        <tr>
                            <th class="dhead">No</th>
                            <th class="dhead">Tanggal</th>
                            <th class="dhead text-end">Stok Program</th>
                            <th class="dhead text-end">Stok Aktual</th>
                            <th class="dhead text-end">Selisih</th>
                            <th class="dhead text-end">Harga Satuan</th>
                            <th class="dhead text-end">Rupiah</th>
                            <th class="dhead">Admin</th>
                        </tr>

                        @php
                            $history = DB::select("SELECT * FROM `tb_rak_telur` WHERE no_nota LIKE '%RAKOPN%'");
                            $getBiaya = DB::selectOne("SELECT sum(a.total_rp + a.biaya_dll) as ttl_rp, sum(a.debit - a.kredit) as ttl_debit FROM `tb_rak_telur` as a
                                    WHERE a.id_gudang = 1 AND a.no_nota LIKE '%RAKMSK%'");
                        @endphp
                        @foreach ($history as $no => $d)
                            @php
                                $hargaSatuan = $getBiaya->ttl_rp / $getBiaya->ttl_debit;
                                $rupiah = $hargaSatuan * $d->selisih;
                            @endphp
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ tanggal($d->tgl) }}</td>
                                <td class="text-end">{{ number_format($d->debit + $d->selisih, 0) }}</td>
                                <td class="text-end">{{ number_format($d->debit, 0) }}</td>
                                <td class="text-end">{{ number_format($d->selisih, 0) }}</td>
                                <td class="text-end">{{ number_format($hargaSatuan, 1) }}</td>
                                <td class="text-end">{{ number_format($rupiah, 1) }}</td>
                                <td>{{ $d->admin }}</td>
                            </tr>
                        @endforeach

                    </table>
                </div>

            </div>
        </div>

        {{-- <table class="table">
            <tr>
                <th class="dhead">Tanggal</th>
                <th class="dhead text-end">Qty</th>
                <th class="dhead text-end">Ttl Rp</th>
                <th class="dhead text-end">Biaya lain-lain</th>
            </tr>

            <tr>
               
            </tr>
        </table> --}}
    </x-theme.modal>
</div>
{{-- end modal --}}
