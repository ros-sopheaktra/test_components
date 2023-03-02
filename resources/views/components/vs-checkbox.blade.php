<div class="{{ isset($checkbox_component_wrapper_class) ? $checkbox_component_wrapper_class : '' }}">
    <label for="{{ $unique_id }}" class="{{ isset($checkbox_label_class) ? $checkbox_label_class : '' }}">{{ isset($checkbox_label_text) ? $checkbox_label_text : '' }}</label>
    <input type="checkbox"
        id    = "{{ $unique_id }}" 
        name  = "{{ isset($name) ? $name : '' }}" 
        class = "custom-checkbox {{ isset($checkbox_class) ? $checkbox_class : '' }}" 
        {{ isset($attributes) ? $attributes : '' }}
    >
</div>