<style>
    .dhead {
        background-color: #435EBE !important;
        color: white;
    }
</style>
<div class="row">
    <div class="col-lg-6">
        <form id="history_serach_opname_mtd">
            <div class="row">
                <div class="col-lg-5">
                    <label for="">Dari</label>
                    <input type="date" class="form-control tgl1" name="tgl1" value="{{$tgl1}}">
                </div>
                <div class="col-lg-5">
                    <label for="">Sampai</label>
                    <input type="date" class="form-control tgl2" name="tgl2" value="{{$tgl2}}">
                </div>
                <div class="col-lg-2">
                    <label for="">Aksi</label>
                    <br>
                    <button type="submit" class="btn btn-sm btn-primary">Search</button>
                </div>
            </div>
        </form>
    </div>

</div>
<br>
<br>
<table class="table table-bordered" id="table_history">
    <thead>
        <tr>
            <th class="dhead" width="5">#</th>
            <th class="dhead">Tanggal</th>
            <th class="dhead">No Nota</th>
            <th class="dhead">Produk</th>
            <th class="dhead text-end">Pcs Program</th>
            <th class="dhead text-end">Kg Program</th>
            <th class="dhead text-end">Pcs Aktual</th>
            <th class="dhead text-end">Kg Aktual</th>
            <th class="dhead text-end">Pcs Selisih</th>
            <th class="dhead text-end">Kg Selisih</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($invoice as $no => $i)
        <tr>
            <td>{{$no+1}}</td>
            <td>{{tanggal($i->tgl)}}</td>
            <td><a href="/opnamecek/{{$i->nota_transfer}}" target="_blank">{{$i->nota_transfer}}</a></td>
            <td>{{$i->nm_telur}}</td>
            <td align="right">{{number_format($i->pcs - ($i->pcs_selisih * -1),0) }}</td>
            <td align="right">{{number_format($i->kg - ($i->kg_selisih * -1),2) }}</td>
            <td align="right">{{number_format($i->pcs,0) }}</td>
            <td align="right">{{number_format($i->kg,2) }}</td>
            <td align="right">{{number_format($i->pcs_selisih,0) }}</td>
            <td align="right">{{number_format($i->kg_selisih,2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>