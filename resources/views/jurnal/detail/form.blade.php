@push('css')
    <link rel="stylesheet" href="{{ asset('vendor/spotlight/css/spotlight.min.css') }}">
@endpush
@php
    $jurnalType = [0 => 'Dana Masuk', 1 => 'Dana Keluar'];
@endphp
<div class="row">
    <x-selectOption name="jurnal_type" label="Jurnal Type" :mandatory="true" :data="$jurnalType" :isKeyId="true"
        :old="$jurnal->jurnal_type ?? ''" />
    <x-selectOption name="jurnal_category_id" label="Jurnal Category" :mandatory="true" :data="$category" defaultId="uuid"
        :old="$jurnal->jurnal_category_id ?? ''" />
    <x-textInput name="date" label="Tanggal Transaksi" :mandatory="true" :old="$jurnal->date ?? ''" type="date"
        :old="formatTimeRaw($jurnal->date ?? '-')" />

    <x-textInput name="balance" label="Balance ({{ $jurnalBalance->Kurs?->symbol }})" :mandatory="true"
        :old="$jurnal->balance ?? ''" />

    <x-textInput name="keterangan" label="Keterangan Transaksi" :mandatory="true" :old="$jurnal->name ?? ''" :isTextArea="true"
        :old="$jurnal->keterangan ?? ''" />
    <x-textInput name="kegiatan" label="Kegiatan Jurnal" :mandatory="true" :old="$jurnal->name ?? ''" :isTextArea="true"
        :old="$jurnal->kegiatan ?? ''" />
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
</div>

<input type="hidden" id="balance_raw" name="balance_raw" value="{{ old('balance_raw', $jurnal->balance ?? '') }}">
@push('js')
    <script src="{{ asset('vendor/spotlight/js/spotlight.min.js') }}"></script>

    <script>
        $(document).ready(function() {

        });
    </script>
@endpush
