@push('css')
    <link rel="stylesheet" href="{{ asset('vendor/uploader/uploader.css') }}">
@endpush
@php
    $type = [0 => 'From Clients', 1 => 'To Employee'];
@endphp
<div class="row">
    <x-textInput name="contract_name" label="Contract Name" :mandatory="true" :old="$contract->contract_name ?? ''" />



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
