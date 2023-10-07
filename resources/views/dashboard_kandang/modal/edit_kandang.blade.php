<form action="{{ route('data_kandang.update') }}" method="post">
    @csrf
    <input type="hidden" name="route" value="dashboard_kandang.index">
    <x-theme.modal title="Edit Kandang" idModal="edit_kandang">
        <div class="row">
            <div id="load-edit"></div>
        </div>
    </x-theme.modal>
</form>
