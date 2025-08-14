<div class="row">
    <x-textInput name="name" label="Permission Group Name" :mandatory="true" :old="$permissionGroup->name ?? ''" col="6" />
    <x-textInput name="description" label="Permission Description" :mandatory="false" :old="$permissionGroup->description ?? ''" col="6" />

</div>
