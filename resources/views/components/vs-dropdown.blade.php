<!-- [ Component Structure Guide ]
    (!) two possibilities of the select should be:
        # static  : 
            - use object to form an enum [ object freeze ]
            - javscript will be then use the object to loop and 
                generate the options and bind into the select tag.
        # dynamic : 
            - data will be come from the backend in the response 
                of JSON format.
            - javascript will be then decode the JSON into object
            - maybe we should reuse the same function to generate
                the options and bind into the select tag.
-->

<div class="{{ isset($component_wrapper_class) ? $component_wrapper_class : '' }}">
    <select 
        id    = "select-component-{{ $unique_id }}-id" 
        name  = "{{ isset($name) ? $name : '' }}" 
        class = "custom-select {{ isset($select_class) ? $select_class : '' }}" 
        {{ isset($attributes) ? $attributes : '' }}
    >
        @unless ( empty($prompt) )
            <option value="" selected hidden>{{ ucwords($prompt) }}</option>
        @endunless
    </select>

    @if ( isset($select_type) 
        && $select_type === 'dynamic' 
        && isset($dynamic_options)
    )
        <input 
            type  = "hidden" 
            id    = "hidden-dynamic-selected-component-{{ $unique_id }}-id" 
            value = "{{$productVariantsCollections}}"
        >
    @endif
</div>
