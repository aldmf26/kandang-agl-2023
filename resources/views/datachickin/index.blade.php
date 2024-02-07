<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">

        <h5 class="float-start mt-1">{{ $title }}</h5>
        <div class="row justify-content-end">

            <div class="col-lg-12">
            </div>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <table class="table table-hover table-bordered" id="table">
                <thead>
                    <tr>
                        <th width="5">#</th>
                        <th class="text-center">Tgl Chick in</th>
                        <th class="text-center">Kandang</th>
                        <th class="text-center">Pop awal</th>
                        <th class="text-end">mati / death</th>
                        <th class="text-end">jual / culling</th>
                        <th class="text-end">jual afkir</th>
                        <th class="text-end">Total DC</th>

                    </tr>
                </thead>
                @foreach ($kandang as $no => $k)
                    <tr>
                        <td>{{ $no + 1 }}</td>
                        <td>{{ tanggal($k->chick_in) }}</td>
                        <td>{{ $k->nm_kandang }}</td>
                        <td class="text-end">{{ number_format($k->stok_awal, 0) }}</td>
                        <td class="text-end">{{ number_format($k->mati, 0) }}</td>
                        <td class="text-end">{{ number_format($k->jual, 0) }}</td>
                        <td class="text-end">{{ number_format($k->afkir) }}</td>
                        <td
                            class="text-end {{ $k->mati + $k->jual + $k->afkir != $k->stok_awal ? 'text-danger' : '' }}">
                            {{ number_format($k->mati + $k->jual + $k->afkir, 0) }}
                        </td>

                    </tr>
                @endforeach

                <tbody>

                </tbody>
            </table>
        </section>
    </x-slot>
    @section('js')
        <script>
            edit('edit_kandang', 'id_kandang', 'data_kandang/edit', 'load-edit')
        </script>
    @endsection
</x-theme.app>
