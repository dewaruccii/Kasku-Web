<div class="row">
    <x-textInput name="name" label="Role Name" :mandatory="true" :old="$role->name ?? ''" />
    <x-textInput name="guard_name" label="Guard Name" :mandatory="true" :old="$role->guard_name ?? ''" />
</div>

<h1 class="mt-5">Permissions <span class="text-danger">*</span></h1>
@error('permissions')
    <small class="text-danger fst-italic">{{ $message }}</small>
@enderror
<hr>
<div class="row">

    @foreach ($permissionsGroup->sortBy('name') as $item)
        <div class="col-md-3">
            <h4>{{ $item->name }} <span style="font-size: 10px">({{ $item->description }})</span>
            </h4>
            <div class="row">
                @foreach ($item->Permission->sortBy('name') as $item1)
                    <div class="col-md-12">
                        <input type="checkbox" class="form-check-input" name="permissions[]" id="{{ $item1->uuid }}"
                            value="{{ $item1->id }}"
                            {{ in_array($item1->id, old('permissions', $permissions ?? [])) ? 'checked' : '' }}>
                        <label for="{{ $item1->uuid }}">{{ $item1->name }}</label>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
