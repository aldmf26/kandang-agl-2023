@props([
    'idModal' => '',
    'size' => '',
    'title' => '',
    'btnSave' => 'Y',
    'scroll' => '',
])

<div id="{{ $idModal }}" {{ $attributes }} class="modal fade" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog  {{empty($scroll) ? '' : 'modal-dialog-scrollable' }}  {{ $size }}" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    {{ $title }}
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <span class="d-none d-sm-block">Close</span>
                </button>
                @if ($btnSave == 'Y')
                <button type="submit" class="btn btn-primary button-save-modal">Simpan</button>
                <button class="btn btn-primary button-save-modal-loading" type="button" disabled hidden>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Loading...
                </button>
                @endif

            </div>

        </div>
    </div>
</div>