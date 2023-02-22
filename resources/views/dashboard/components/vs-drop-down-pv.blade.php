{{-- product list select --}}
<div class="{{$main_div_class}}">
    <select class="{{$select_class}}" name="{{$select_name}}" id="{{$select_uniqe_id}}" {{$attr}}>
        @if ($dropdowntype == "static")
            @foreach ($productVariants as $productVariant)
                <option value="{{$productVariant->id}}" data-price="{{$productVariant->price}}" data-cost="{{$productVariant->cost}}" product_va_name="{{$productVariant->name}}" data-quantity="{{$productVariant->quantity}}">{{$productVariant->name}}</option>
            @endforeach
        @endif
    </select>
</div>