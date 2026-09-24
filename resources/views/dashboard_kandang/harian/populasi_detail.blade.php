<h6 class="mb-3">Tanggal : <span>{{ tanggal($tgl) }}</span></h6>
<div class="table-responsive">
    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th class="dhead text-center" style="width:40px">#</th>
                <th class="dhead text-center">Kandang</th>
                <th class="dhead text-end">Mati</th>
                <th class="dhead text-end">Jual</th>
                <th class="dhead text-end">Afkir</th>
                <th class="dhead text-end">Total Kurang</th>
            </tr>
        </thead>
        <tbody>
            @php
                $ttlMati = 0;
                $ttlJual = 0;
                $ttlAfkir = 0;
            @endphp
            @forelse($rows as $i => $d)
                @php
                    $ttlMati += $d->mati;
                    $ttlJual += $d->jual;
                    $ttlAfkir += $d->afkir;
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="fw-semibold">{{ $d->nm_kandang }}</td>
                    <td class="text-end">{{ number_format($d->mati, 0) }}</td>
                    <td class="text-end">{{ number_format($d->jual, 0) }}</td>
                    <td class="text-end">{{ number_format($d->afkir, 0) }}</td>
                    <td class="text-end">{{ number_format($d->mati + $d->jual + $d->afkir, 0) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada data populasi tanggal ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if ($rows->isNotEmpty())
            <tfoot>
                <tr>
                    <th colspan="2" class="text-center">TOTAL</th>
                    <th class="text-end">{{ number_format($ttlMati, 0) }}</th>
                    <th class="text-end">{{ number_format($ttlJual, 0) }}</th>
                    <th class="text-end">{{ number_format($ttlAfkir, 0) }}</th>
                    <th class="text-end">{{ number_format($ttlMati + $ttlJual + $ttlAfkir, 0) }}</th>
                </tr>
            </tfoot>
        @endif
    </table>
</div>
