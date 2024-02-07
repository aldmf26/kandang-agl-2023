<div class="row">
    <h6>Tanggl : <span>{{ tanggal($pop[0]->tgl) }}</span></h6>
    <div class="col-lg-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="bg-primary text-white">Kdg</th>
                    <th class="text-end bg-primary text-white">Mati / Death</th>
                    <th class="text-end bg-primary text-white">Jual / Culling</th>
                    <th class="text-end bg-primary text-white">Afkir</th>
                </tr>
            </thead>
            @php
                $ttlMati = 0;
                $ttlJual = 0;
                $ttlAfkir = 0;
            @endphp
            @foreach ($pop as $d)
                @php
                    $ttlMati += $d->mati;
                    $ttlJual += $d->jual;
                    $ttlAfkir += $d->afkir;
                @endphp
                <tr>
                    <td>{{ $d->nm_kandang }}</td>
                    <td align="right">{{ $d->mati }}</td>
                    <td align="right">{{ $d->jual }}</td>
                    <td align="right">{{ $d->afkir }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>TOTAL</th>
                    <th class="text-end">{{ $ttlMati }}</th>
                    <th class="text-end">{{ $ttlJual }}</th>
                    <th class="text-end">{{ $ttlAfkir }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
