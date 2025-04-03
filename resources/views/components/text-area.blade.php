<div class="forms-field forms-field-textarea" data-field="{{ $name }}">
    <label class="forms-field-label" for="{{ $name }}">
        {{ $label }}
        @if ($required)
            <span class="forms-required-label">*</span>
        @endif
    </label>
    <textarea id="{{ $name }}" name="{{ $name }}" aria-errormessage="{{ $name }}-error"
        class="forms-field-medium {{ $required ? 'forms-field-required' : '' }}">{{ old($name, $value) }}</textarea>
    <em id="{{ $name }}-error" class="forms-error" role="alert" aria-label="Error message"
        for="{{ $name }}" @readonly(true) aria-hidden="true">
        This field is required.
    </em>

    @if (!empty($description))
        <div class="forms-field-description">{{ $description }}</div>
    @endif
</div>
