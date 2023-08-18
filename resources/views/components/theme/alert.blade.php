@props([
    'pesan' => '',
])
<div class="col-lg-4">
    <div class="alert alert-danger">
        <h6 class="text-white"> {{ ucwords($pesan) }}.</h6>
    </div>
</div>
