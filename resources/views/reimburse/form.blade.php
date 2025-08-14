@push('css')
    <link rel="stylesheet" href="{{ asset('vendor/uploader/uploader.css') }}">
@endpush
@php
    $type = [0 => 'From Clients', 1 => 'To Employee'];
@endphp
<div class="row">
    <x-textInput name="justifikasi" label="Justifikasi" :mandatory="true" :old="$reimburse->justifikasi ?? ''" />
    <x-textInput name="total" label="Total Reimburse" :mandatory="true" :old="$reimburse->total ?? ''" />
    <x-textInput name="date" label="Reimburse Date" type="date" :old="formatTimeRaw($reimburse->date ?? '-')" />

    <x-selectOption name="type" label="Reimburse Type" :mandatory="true" :data="$type" :isKeyId="true"
        :old="$reimburse->type ?? ''" />
    <x-selectOption name="jurnal_id" label="Jurnal" :mandatory="true" :data="$kurs" :old="$reimburse->jurnal_id ?? ''"
        defaultId="uuid" defaultName="name" />

    @if (Route::is(['reimburses.edit']))
        <x-formUpload name="evidence" title="Evidence" class="evidence" :mandatory="false" />


        <div class="col-md-6 ">
            <label for="File">File Evidence</label>
            <div class="row">
                @foreach ($reimburse?->Jurnal->Attachments as $item1)
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
    <input type="hidden" id="total_raw" name="total_raw" value="{{ old('total_raw', $jurnal->balance ?? '') }}">

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
            $("#kurs_id").on('change', function() {
                console.log($(this).val());

            });
            var cleave = new Cleave('#total', {
                numeral: true,
                numeralDecimalMark: '.', // Tetap gunakan koma, jika ada desimal dihapus
                delimiter: ',', // Gunakan titik sebagai pemisah ribuan
                // prefix: 'Rp. ', // Simbol mata uang
                noImmediatePrefix: true, // Simbol mata uang muncul hanya saat ada input
                numeralDecimalScale: 0, // Tidak ada angka desimal (0 desimal di belakang koma)
                numeralThousandsGroupStyle: 'thousand', // Format ribuan
            });
            $("#total").on('input', function() {
                let formattedAngka = cleave.getRawValue();
                $("#total_raw").val(formattedAngka);

            });

        })
    </script>
@endpush
