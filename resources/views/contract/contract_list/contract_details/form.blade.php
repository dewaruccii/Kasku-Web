@push('css')
    <link rel="stylesheet" href="{{ asset('vendor/uploader/uploader.css') }}">
    <style>
        .hide {
            display: none;
        }
    </style>
@endpush
@php
    $type = [0 => 'Pembayaran', 1 => 'Pemasukan'];
@endphp
@php
    $isBonus = [0 => 'No', 1 => 'Yes'];
@endphp
<div class="row">
    <x-textInput name="description" label="Description" :mandatory="true" :old="$contractDetail->description ?? ''" />
    <x-selectOption name="is_bonus" label="Bonus ?" :mandatory="true" :data="$isBonus" :isKeyId="true"
        :old="$contractDetail->is_bonus ?? ''" />
    <x-selectOption name="jurnal_type" label="Type" :mandatory="true" :data="$type" :isKeyId="true"
        :old="$contractDetail->type ?? ''" class="hide" />
    <x-selectOption name="kurs_id" label="Kurs" :mandatory="true" :data="$jurnalBalance" :old="$contractDetail->jurnal_balance_id ?? ''"
        defaultId="uuid" defaultName="name" />
    <x-textInput name="transaction_date" label="Tanggal Transaksi" type="date" :mandatory="true"
        :old="formatTimeRaw($contractDetail->transaction_date ?? '-')" />
    <x-textInput name="total" label="Total Nominal" :mandatory="true" :old="$contractDetail->total ?? ''" />
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
    <input type="hidden" id="total_raw" name="total_raw" value="{{ old('total_raw', $jurnal->balance ?? '') }}">

</div>
@push('js')
    <script script src="{{ asset('vendor/uploader/uploader.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js"></script>
    @if (old('is_bonus', $contractDetail->is_bonus ?? '') == 0)
        <script>
            let val = '{{ old('is_bonus', $contractDetail->is_bonus ?? '') }}';
            if (val == 0) {

                $(".hide").show();
            } else {

                $(".hide").hide();
            }
        </script>
    @endif
    <script>
        $(document).ready(function() {
            $('.evidence').imageUploader({
                label: "Drag & Drop files here or click to browse",
                imagesInputName: 'evidence',

            });
            $("#is_bonus").on('change', function() {
                let val = $(this).val();
                if (val == 0) {

                    $(".hide").show();
                } else {

                    $(".hide").hide();
                }

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
