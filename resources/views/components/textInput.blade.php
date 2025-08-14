@props([
    'name',
    'label',
    'old' => '',
    'col' => 6,
    'type' => 'text',
    'classInput' => '',
    'class' => '',
    'placeholder' => '',
    'mandatory' => false,
    'isTextArea' => false,
])

<div class="col-md-{{ $col }} {{ $class }}">
    <div class="form-group">
        @if ($type != 'hidden')

            <label for="{{ $name }}">{{ $label }} @if ($mandatory)
                    <span class="text-danger">*</span>
                @endif
            </label>
        @endif
        @if (!$isTextArea)
            <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}"
                class="form-control @error($name)
                is-invalid
            @enderror {{ $classInput }}"
                placeholder="{{ $placeholder }}" value="{{ old($name, $old) }}">
        @else
            <textarea name="{{ $name }}" id="{{ $name }}"
                class="form-control @error($name)
                is-invalid
            @enderror {{ $classInput }}"
                placeholder="{{ $placeholder }}">{{ old($name, $old) }}</textarea>
        @endif
        @error($name)
            <small class="fst-italic text-danger errorValidation error-{{ $name }}">{{ $message }}</small>
        @enderror
        <small class="fst-italic text-danger errorValidation error-{{ $name }}"></small>

    </div>

</div>
