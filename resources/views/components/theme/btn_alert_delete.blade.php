@props([
'route' => '',
'name' => '',
'tgl1' => '',
'tgl2' => '',
'id_proyek' => '',
])
<form action="{{ route($route) }}" method="get">
    <div class="modal fade" id="delete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <h5 class="text-danger ms-4 mt-4"><i class="fas fa-trash"></i> Hapus Data</h5>
                        <p class=" ms-4 mt-4">Apa anda yakin ingin menghapus ?</p>
                        <input type="hidden" class="no_nota" name="{{$name}}">
                        <input type="hidden" name="tgl1" value="{{ $tgl1 ?? '' }}">
                        <input type="hidden" name="tgl2" value="{{ $tgl2 ?? '' }}">
                        <input type="hidden" name="id_proyek" value="{{ $id_proyek ?? '' }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </div>
        </div>
    </div>
</form>
@section('scripts')
<script>
    $(document).on('click', '.delete_nota', function() {
            var no_nota = $(this).attr('no_nota');
            $('.no_nota').val(no_nota);
        })
</script>
@endsection