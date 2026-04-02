@php
    $fieldId = $id ?? $name;
@endphp

<div class="forms-field forms-field-text" data-field="{{ $name }}">
    <label class="forms-field-label" for="{{ $fieldId }}">
        @if (!empty($label))
            {{ $label }}
        @endif
        @if ($required == true)
            <span class="forms-required-label">*</span>
        @endif
    </label>
    <input type="text" id="{{ $fieldId }}" name="{{ $name }}" value="{{ old($name, $value) }}"
        aria-errormessage="{{ $name }}-error"
        class="forms-field-medium {{ $required ? 'forms-field-required' : '' }}"
        @if ($required == true) required @endif />
    <em id="{{ $fieldId }}-error" class="forms-error" role="alert" aria-label="Error message"
        for="{{ $name }}" @readonly(true) aria-hidden="true">
        This field is required.
    </em>
    @if (!empty($description))
        <div class="forms-field-description">{{ $description }}</div>
    @endif
</div>
