@extends('dashboard.layout.index')

@section('stylesheet')
    {{-- <link rel="stylesheet" href="{{ asset('css/dashboard/vs-table.css') }}"> --}}
@endsection

@section('content')
    <div class="w-75 m-auto py-4">
        <h2 class="text-center">Welcome To <span class="bg-warning text-danger">[ Table ]</span> Component Demo Page</h2>
    </div>
    <div class="component-use-case-wrapper p-3 m-auto" style="width: 80%; height: 80vh;">
        <!-- seach content container -->
        @include('components.vs-table', [
            'thead_unique_element_id'  => 'product_thead_id',
            'tbody_unique_element_id'  => 'product_tbody_id',
            'table_wrapper_class'      => 'table-bordered table-striped',
            'is_active_search'         => false,
            ]
        )
    </div>
@endsection

@section('script')
    <script src="{{ asset('js/components/vs-table.js') }}"></script>
    <script src="{{ asset('js/utils/text_manippulations.js') }}"></script>
    <script src="{{ asset('js/thead/product.js') }}"></script>
    <script src="{{ asset('js/enum/product.js') }}"></script>

    <script>
       $(document).ready(function(){
            // thead
            const getProductThead = getTheadOfProduct();
            generate_thead_table( 'product_thead_id', getProductThead );

            // tbody
            const getProductBodies = getProducts();
            generate_tbody_table( 'product_tbody_id', getProductBodies );
        });
    </script>
@endsection