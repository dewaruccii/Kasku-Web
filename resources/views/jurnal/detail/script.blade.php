<script src="https://cdn.jsdelivr.net/npm/cleave.js"></script>
@if ($jurnalBalance?->Kurs?->code === 'IDR')
    <script>
        $(document).ready(function() {
            var cleave = new Cleave('#balance', {
                numeral: true,
                numeralDecimalMark: '.', // Tetap gunakan koma, jika ada desimal dihapus
                delimiter: ',', // Gunakan titik sebagai pemisah ribuan
                // prefix: 'Rp. ', // Simbol mata uang
                noImmediatePrefix: true, // Simbol mata uang muncul hanya saat ada input
                numeralDecimalScale: 0, // Tidak ada angka desimal (0 desimal di belakang koma)
                numeralThousandsGroupStyle: 'thousand', // Format ribuan
            });
            $("#balance").on('input', function() {
                let formattedAngka = cleave.getRawValue();
                $("#balance_raw").val(formattedAngka);

            });
        })
    </script>
@else
    <script>
        $(document).ready(function() {
            let formatPrefix = '{{ $jurnalBalance?->Kurs?->symbol }}';
            var cleave = new Cleave('#balance', {
                numeral: true,
                numeralDecimalMark: '.',
                delimiter: ',',

                // prefix: formatPrefix + ' ', // Simbol mata uang

            });
            $("#balance").on('input', function() {
                let formattedAngka = cleave.getRawValue();
                $("#balance_raw").val(formattedAngka);

            });
        })
    </script>
@endif
<script script src="{{ asset('vendor/uploader/uploader.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.evidence').imageUploader({
            label: "Drag & Drop files here or click to browse",
            imagesInputName: 'evidence',

        });

    })
</script>
