{{-- <div class="{{$dev_class}}">
    <button 
        type ="{{$btn_type}}" 
        id   ="{{$btn_unique_id}}" 
        class="btn {{$btn_style_class}}" 
        {{$attr}} 
    >
        {{$btn_text}}
    </button>
</div> --}}

<div class="{{ isset($component_wrapper_class) ? $component_wrapper_class : '' }}">
    <button
        type  = "{{ isset($button_type) ? $button_type : '' }}"
        id    = "button-component-{{ $unique_id }}-id"
        class = "btn btn-light {{ isset($button_class) ? $button_class : '' }}"
        {{ isset($attributes) ? $attributes : '' }}
    >
        {{ $button_text }}
    </button>
</div>
