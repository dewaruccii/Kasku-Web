<div class="row">
    <x-textInput name="name" label="Jurnal Name" :mandatory="true" :old="$jurnal->name ?? ''" />
    <x-selectOption name="kurs_id" label="Kur" :mandatory="true" :data="$kurs" defaultName="code" defaultId="uuid"
        :old="$jurnal->kurs_id ?? ''" />
</div>
