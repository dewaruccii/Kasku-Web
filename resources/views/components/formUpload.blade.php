@props(['name' => '', 'title' => '', 'default' => '', 'class' => 'image-uploader', 'mandatory' => false])

<div class="col-md-6">

    <div class="form-group">
        <label for="{{ $name }}">{{ $title }}
            @if ($mandatory)
                <span class="text-danger">*</span>
            @endif
        </label>
        <div class="{{ $class }}"></div>
        @error($name)
            <small class="fst-italic text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>
