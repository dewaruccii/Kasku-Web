@push('css')
    <link rel="stylesheet" href="{{ asset('vendor/uploader/uploader.css') }}">
    <style>
        .hide {
            display: none;
        }
    </style>
@endpush
@php
    $type = [0 => 'From Clients', 1 => 'From Jurnal'];
@endphp
<div class="row">

    <x-selectOption name="type" label="Charge From" :mandatory="true" :data="$type" :isKeyId="true"
        :old="$category->is_active ?? ''" />
    <x-textInput name="date" label="Exchange Date" type="date" />
    <x-textInput name="tanggal_kesepakatan" label="Tanggal Kesepakatan" type="date" class="hide" :mandatory="true" />
    {{-- <x-textInput name="total_kesepakatan" label="Total Kesepakatan" :mandatory="true" class="hide" /> --}}





</div>
<div class="row">
    <x-selectOption name="from" label="From" :mandatory="true" :data="$jurnalBalance" :old="$category->is_active ?? ''" defaultId="uuid"
        defaultName="name" />
    <x-textInput name="from_total" label="From Total" :mandatory="true" />

    <x-selectOption name="to" label="To" :mandatory="true" :data="$jurnalBalance" :old="$category->is_active ?? ''"
        defaultId="uuid" defaultName="name" />
    <x-textInput name="to_total" label="To Total" :mandatory="true" />
    <x-textInput name="fee" label="Fee" :mandatory="true" />

    @if (Route::is(['jurnal.detail.edit']))
        <x-formUpload name="evidence" title="Evidence" class="evidence" :mandatory="false" />


        <div class="col-md-6 ">
            <label for="File">File Evidence</label>
            <div class="row">
                @foreach ($jurnal->Attachments as $item1)
                    <div class="col-md-12 border mt-2">
                        <div class="d-flex align-items-center justify-content-between p-2">
                            <img class="rounded" src="{{ asset('file_icon/' . $item1->ext . '.png') }}" alt=""
                                style="width: 50px">
                            <div class="d-flex flex-column">
                                <a href="{{ asset('storage') . $item1->path }}" data-title="{{ $item1->file_name }}"
                                    class="spotlight">{{ $item1->file_name }}</a>
                                {{-- <h6 style="color:#95a5a6"></h6> --}}
                                <small class="fst-italic"
                                    style="font-size:12px;font-weight:bold;color:#34495e">{{ formatSizeUnits($item1->size) }}</small>
                            </div>
                            {{-- <a href="{{ route('jurnal.balance.download', encString($item->id)) }}">
                            <i class="fas fa-cloud-download-alt " style="font-size: 30px;cursor:pointer;"></i>
                        </a> --}}

                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    @else
        <x-formUpload name="evidence" title="Evidence" class="evidence" :mandatory="true" />
    @endif
    <input type="hidden" id="from_total_raw" name="from_total_raw"
        value="{{ old('from_total_raw', $jurnal->balance ?? '') }}">
    <input type="hidden" id="to_total_raw" name="to_total_raw"
        value="{{ old('to_total_raw', $jurnal->balance ?? '') }}">
    <input type="hidden" id="fee_total_raw" name="fee_total_raw"
        value="{{ old('fee_total_raw', $jurnal->balance ?? '') }}">
</div>
@push('js')
    <script script src="{{ asset('vendor/uploader/uploader.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js"></script>
    @if (old('type', $jurnal->type ?? '') == 0)
        <script>
            $(".hide").show();
        </script>
    @else
        <script>
            $(".hide").show();
        </script>
    @endif
    <script>
        $(document).ready(function() {
            $("#type").on('change', function() {
                let value = $(this).val();
                if (value == 0) {
                    $(".hide").show();
                } else {
                    $(".hide").hide();

                }
            });
            $('.evidence').imageUploader({
                label: "Drag & Drop files here or click to browse",
                imagesInputName: 'evidence',

            });
            $("#kurs_id").on('change', function() {
                console.log($(this).val());

            });
            var cleave1 = new Cleave('#from_total', {
                numeral: true,
                numeralDecimalMark: '.', // Tetap gunakan koma, jika ada desimal dihapus
                delimiter: ',', // Gunakan titik sebagai pemisah ribuan
                // prefix: 'Rp. ', // Simbol mata uang
                noImmediatePrefix: true, // Simbol mata uang muncul hanya saat ada input
                numeralDecimalScale: 0, // Tidak ada angka desimal (0 desimal di belakang koma)
                numeralThousandsGroupStyle: 'thousand', // Format ribuan
            });
            var cleave2 = new Cleave('#to_total', {
                numeral: true,
                numeralDecimalMark: '.', // Tetap gunakan koma, jika ada desimal dihapus
                delimiter: ',', // Gunakan titik sebagai pemisah ribuan
                // prefix: 'Rp. ', // Simbol mata uang
                noImmediatePrefix: true, // Simbol mata uang muncul hanya saat ada input
                numeralDecimalScale: 0, // Tidak ada angka desimal (0 desimal di belakang koma)
                numeralThousandsGroupStyle: 'thousand', // Format ribuan
            });
            var cleave3 = new Cleave('#fee', {
                numeral: true,
                numeralDecimalMark: '.', // Tetap gunakan koma, jika ada desimal dihapus
                delimiter: ',', // Gunakan titik sebagai pemisah ribuan
                // prefix: 'Rp. ', // Simbol mata uang
                noImmediatePrefix: true, // Simbol mata uang muncul hanya saat ada input
                numeralDecimalScale: 0, // Tidak ada angka desimal (0 desimal di belakang koma)
                numeralThousandsGroupStyle: 'thousand', // Format ribuan
            });
            $("#from_total").on('input', function() {
                let formattedAngka = cleave1.getRawValue();
                $("#from_total_raw").val(formattedAngka);

            });
            $("#to_total").on('input', function() {
                let formattedAngka = cleave2.getRawValue();
                $("#to_total_raw").val(formattedAngka);
            });
            $("#fee").on('input', function() {
                let formattedAngka = cleave3.getRawValue();
                $("#fee_total_raw").val(formattedAngka);

            });

        })
    </script>
@endpush
