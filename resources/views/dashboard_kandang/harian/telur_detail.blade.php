<h6 class="mb-1">Kandang : <span>{{ $kandang->nm_kandang ?? '' }}</span></h6>
<h6 class="mb-3">Tanggal : <span>{{ tanggal($tgl) }}</span></h6>
<div class="table-responsive">
    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th class="dhead text-center" style="width:40px">#</th>
                <th class="dhead text-center">Produk</th>
                <th class="dhead text-end">Pcs</th>
                <th class="dhead text-end">Kg</th>
            </tr>
        </thead>
        <tbody>
            @php
                $ttlPcs = 0;
                $ttlKg = 0;
            @endphp
            @forelse($rows as $i => $d)
                @php
                    $ttlPcs += $d->pcs;
                    $ttlKg += $d->kg;
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ ucwords($d->nm_telur) }}</td>
                    <td class="text-end">{{ number_format($d->pcs, 0) }}</td>
                    <td class="text-end">{{ number_format($d->kg, 1, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">Belum ada data telur.</td>
                </tr>
            @endforelse
        </tbody>
        @if ($rows->isNotEmpty())
            <tfoot>
                <tr>
                    <th colspan="2" class="text-center">TOTAL</th>
                    <th class="text-end">{{ number_format($ttlPcs, 0) }}</th>
                    <th class="text-end">{{ number_format($ttlKg, 1, ',', '.') }}</th>
                </tr>
            </tfoot>
        @endif
    </table>
</div>
