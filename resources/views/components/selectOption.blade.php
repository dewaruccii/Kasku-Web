@props([
    'name',
    'label',
    'data' => [],
    'col' => 6,
    'classSelect' => '',
    'class' => '',
    'defaultId' => 'id',
    'defaultName' => 'name',
    'isKeyId' => false,
    'isMulti' => false,
    'old' => '',
    'oldArray' => [],
    'mandatory' => false,
])

<div class="col-md-{{ $col }} {{ $class }}">
    <div class="form-group">
        <label for="{{ $name }}">{{ $label }} @if ($mandatory)
                <span class="text-danger">*</span>
            @endif
        </label>
        <select name="{{ $isMulti ? $name . '[]' : $name }}" id="{{ $name }}"
            class="select2 @error($name) is-invalid @enderror {{ $classSelect }}"
            @if ($isMulti) multiple @endif>
            <option value="">-- Select {{ $label }} --</option>
            @foreach ($data as $key => $item)
                @if ($isKeyId)
                    <option value="{{ $key }}"
                        {{ (string) old($name, $old) === (string) $key ? 'selected' : '' }}>
                        {{ $item }}</option>
                @else
                    @if ($isMulti)
                        <option value="{{ $item->$defaultId }}"
                            {{ in_array($item->$defaultId, old($name, $oldArray)) ? 'selected' : '' }}>
                            {{ $item->$defaultName }}</option>
                    @else
                        <option value="{{ $item->$defaultId }}"
                            {{ old($name, $old) == $item->$defaultId ? 'selected' : '' }}>
                            {{ $item->$defaultName }}</option>
                    @endif
                @endif
            @endforeach
        </select>
    </div>
    @error($name)
        <small class="fst-italic text-danger">{{ $message }}</small>
    @enderror
</div>
