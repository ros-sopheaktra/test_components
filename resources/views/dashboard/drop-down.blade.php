@extends('dashboard.layout.index')

{{-- custom stylesheet --}}
@section('stylesheet')
<link rel="stylesheet" href="{{asset('css/dashboard/components/vs-drop-down-pv.css')}}">
{{-- search selecte --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
@endsection

{{-- BEGIN:: Adjustment --}}
@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            {{-- drop donw component --}}
            @include('dashboard.components.vs-drop-down-pv', [
                'main_div_class'  => 'product-list-select-wrapper bg-blue-old mt-4',
                'select_class'    => 'form-control serchproduct',
                'select_name'     => 'product_va_id',
                'select_uniqe_id' => 'product_va_id',
                'attr'            => "",
                'dropdowntype'    => 'static',
                'dropdowdata'     => $productVariants,
                ]
            )
            <a class="btn btn-outline-success mt-3" href="{{route('input-test')}}">Input testing</a>
        </div>
    </div>
</div>

@endsection
{{-- ENG:: Adjustment --}}

{{-- custom script --}}
@section('script')
    {{-- select search --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    {{-- custom script --}}
    <script src="{{ asset('js/dashboard/components/vs-drop-down-pv.js') }}"></script>
    <script>
        // multi select product variant and customer
        $('.serchproduct').select2();

        // call select function
        $(document).ready(function(){
            selectOnChange('#product_va_id', showAlert);
        });

        function showAlert(){
            alert('hi');
        }
    </script>
@endsection