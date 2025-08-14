@php
    $isActive = [0 => 'No Active', 1 => 'Active'];
@endphp
<div class="row">
    <x-textInput name="name" label="Category Name" :mandatory="true" :old="$category->name ?? ''" />
    <x-selectOption name="is_active" label="Active ?" :mandatory="true" :data="$isActive" :isKeyId="true"
        :old="$category->is_active ?? ''" />


</div>
