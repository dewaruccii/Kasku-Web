@push('css')
    <link rel="stylesheet" href="{{ asset('vendor/uploader/uploader.css') }}">
@endpush
@php
    $type = [0 => 'From Clients', 1 => 'To Employee'];
@endphp
<div class="row">
    <x-textInput name="materi_kuasa" label="Materi Kuasa" :mandatory="true" :old="$contractList->materi_kuasa ?? ''" />

    <x-textInput name="tanggal_kuasa" label="Tanggal Kuasa" type="date" :mandatory="true" :old="formatTimeRaw($contractList->tanggal_kuasa ?? '-')" />
    <x-textInput name="tanggal_contract" label="Tanggal Kontrak" type="date" :mandatory="true" :old="formatTimeRaw($contractList->tanggal_contract ?? '-')" />

</div>
@push('js')
    <script script src="{{ asset('vendor/uploader/uploader.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js"></script>

    <script>
        $(document).ready(function() {
            $('.evidence').imageUploader({
                label: "Drag & Drop files here or click to browse",
                imagesInputName: 'evidence',

            });



        })
    </script>
@endpush
