{{-- <a href="{{ route('penjualan2.print', ['urutan' => $head_jurnal->urutan]) }}" class="btn btn-sm btn-primary float-end"
        target="_blank"><i class="fas fa-print"></i> Print</a> --}}
<br>
<br>
<style>
    .dhead {
        background-color: #435EBE !important;
        color: white;
    }

    .dborder {
        border-color: #435EBE
    }
</style>
<table class="table table-hover table-bordered dborder">
    <thead>
        <tr>
            <th class="dhead" width="5">#</th>
            <th class="dhead">Nota</th>
            <th class="dhead">Nama Pelanggan</th>
            <th width="15%" class="dhead" style="text-align: right">Rp Satuan</th>
            <th width="15%" class="dhead text-center">Qty</th>
            <th width="15%" class="dhead" style="text-align: right">Total Rp</th>
            <th width="15%" class="dhead">Admin</th>
        </tr>
    </thead>
    <tbody>
        @php
            $ttlQty = 0;
            $ttlRp = 0;
        @endphp
        @foreach ($produk as $no => $a)
            @php
                
                $ttlQty += $a->qty;
                $ttlRp += $a->total_rp;
            @endphp
            <tr>
                <td>{{ $no + 1 }}</td>
                <td>PUM-{{ $a->urutan }}</td>
                <td>{{ $a->id_customer }}</td>
                <td align="right">{{ number_format($a->rp_satuan, 0) }}</td>
                <td align="center">{{ $a->qty }}</td>
                <td align="right">{{ number_format($a->total_rp, 0) }}</td>
                <td>{{ $a->admin }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4" class="text-center">Total</th>
            <th class="text-center">{{ $ttlQty }}</th>
            <th class="text-end">{{ number_format($ttlRp, 0) }}</th>
            <th></th>
        </tr>
    </tfoot>
</table>
