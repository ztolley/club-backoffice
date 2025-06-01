@php
    $fieldId = $id ?? $name;
    $value = $value ?? null;
@endphp

<div class="forms-field forms-field-select" data-field="{{ $name }}">
    <label class="forms-field-label" for="{{ $fieldId }}">
        @if (!empty($label))
            {{ $label }}
        @endif
        @if ($required == true)
            <span class="forms-required-label">*</span>
        @endif
    </label>
    <select id="{{ $fieldId }}" name="{{ $name }}" aria-errormessage="{{ $name }}-error"
        class="forms-field-medium {{ $required ? 'forms-field-required' : '' }}"
        @if ($required == true) required @endif>
        @if (!empty($placeholder))
            <option value="" disabled {{ old($name, $value) ? '' : 'selected' }}>{{ $placeholder }}</option>
        @endif
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>
    <em id="{{ $fieldId }}-error" class="forms-error" role="alert" aria-label="Error message"
        for="{{ $name }}" @readonly(true) aria-hidden="true">
        This field is required.
    </em>
    @if (!empty($description))
        <div class="forms-field-description">{{ $description }}</div>
    @endif
</div>
