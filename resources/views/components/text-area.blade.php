@php
    $fieldId = $id ?? $name;
@endphp
<div class="forms-field forms-field-textarea" data-field="{{ $name }}">
    <label class="forms-field-label" for="{{ $fieldId }}">
        @if (!empty($label))
            {{ $label }}
        @endif
        @if ($required)
            <span class="forms-required-label">*</span>
        @endif
    </label>
    <textarea id="{{ $fieldId }}" name="{{ $name }}" aria-errormessage="{{ $name }}-error"
        class="forms-field-medium {{ $required ? 'forms-field-required' : '' }}"
        @if ($required == true) required @endif>{{ old($name, $value) }}</textarea>
    <em id="{{ $fieldId }}-error" class="forms-error" role="alert" aria-label="Error message"
        for="{{ $name }}" @readonly(true) aria-hidden="true">
        This field is required.
    </em>

    @if (!empty($description))
        <div class="forms-field-description">{{ $description }}</div>
    @endif
</div>
