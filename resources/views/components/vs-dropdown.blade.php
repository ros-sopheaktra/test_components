{{-- product list select --}}
{{-- <div class="{{$main_div_class}}">
    <select 
        id    = "{{ $select_uniqe_id }}" 
        name  = "{{ $select_name }}" 
        class = "{{ $select_class }}" 
        {{ $attr }}
    >
        @if ($dropdowntype == "static")
            @foreach ($productVariants as $productVariant)
                <option 
                    value = "{{$productVariant->id}}" 
                    data-price = "{{$productVariant->price}}" 
                    data-cost = "{{$productVariant->cost}}" 
                    product_va_name = "{{$productVariant->name}}" 
                    data-quantity = "{{$productVariant->quantity}}"
                >
                    {{ $productVariant->name }}
                </option>
            @endforeach
        @endif
    </select>
</div> --}}

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

        @if ($selectType === 'static')
            <option 
                value = ""
                {{ isset($option_attributes) ? $option_attributes : '' }}
            >
                Demo Value
            </option>
        @else
            @foreach ($collections as $item)
                <option value=""></option>
            @endforeach
        @endif
    </select>    
</div>
