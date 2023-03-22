@extends('dashboard.layout.index')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('css/components/vs-table.css') }}">
@endsection

@section('content')
    <div class="w-75 m-auto py-4">
        <h2 class="text-center">Welcome To <span class="bg-warning text-danger">[ Drop Down ]</span> Component Demo Page</h2>
    </div>
    <div class="component-use-case-wrapper bg-dark p-3 m-auto" style="width: 80%; height: 80vh;">
        <!-- simple component that alert any value after selected ( on change deteched ) -->
        @include('components.vs-dropdown', [
            'unique_id'               => 'select-code-001',
            'prompt'                  => 'select month',
            'component_wrapper_class' => 'mb-3'
        ])

        <!-- simple component that alert any value after selected ( on change deteched ) -->
        @include('components.vs-dropdown', [
            'unique_id'               => 'select-code-002',
            'prompt'                  => 'select currency',
            'component_wrapper_class' => 'mb-3'
        ])

        <!-- dynamic selection -->
        @include('components.vs-dropdown', [
            'component_wrapper_class' => 'mt-3',
            'unique_id'               => 'select-code-003',
            'prompt'                  => 'select product varaint',
            'select_type'             => 'dynamic',
            'dynamic_options'         => "$productVariantsCollections",
        ])

        <!-- table -->
        @include('components.vs-table', [
            'component_wrapper_class' => 'mt-4',
            'table_wrapper_class'     => 'table-bordered text-white',
            'thead_id'                => 'product_thead_id',
            'tbody_id'                => 'product_tbody_id',
            'has_filter'              => true,
            'table_id'                => 'product_table',
            'table_filter_id'         => 'input_search_box_id',
            'table_filter_class'      => 'search_box_class',
            ]
        )
    </div>
@endsection

@section('script')
    <script src="{{ asset('js/enum/months.js') }}"></script>
    <script src="{{ asset('js/enum/currencies.js') }}"></script>
    <script src="{{ asset('js/utils/text_manippulations.js') }}"></script>

    <script src="{{ asset('js/thead/product.js') }}"></script>
    <script src="{{ asset('js/components/vs-dropdown.js') }}"></script>
    <script src="{{ asset('js/components/vs-table.js') }}"></script>
    <script src="{{ asset('js/components/vs-search-table.js') }}"></script>

    <script>
        $(document).ready(function(){
            // example dropdown component with static value
            const months = getMonths();
            generate_selection_options( 'select-code-001', months );

            // example dropdown component with static value and custom method on selected
            const currencies = getCurrencies();
            generate_selection_options( 'select-code-002', currencies );
            vsSelectOnChange('select-code-002', function(){ alert($(this).val()); });

            // dropdown selection mix with table component
            generate_selection_options( 'select-code-003', get_hidden_dynamic_options('select-code-003') );

            // thead
            const getProductThead = getTheadOfProduct();
            generate_table_thead( 'product_thead_id', getProductThead );

            // validate mandatory fields before process submit
            const listDataInTable = function() {
                const state = $(this);
                const products = Object.freeze([
                    {
                        name           : state.find(":selected").attr('name'),
                        alert_quantity : state.find(":selected").attr('alert_quantity'),
                        product_point  : state.find(":selected").attr('product_point'),
                    },
                ]);
                generate_tbody_table( 'product_tbody_id', products );
            };
            vsSelectOnChange( 'select-code-003', listDataInTable );
        });

        //search
        filterInTable('input_search_box_id', 'product_table');
    </script>
@endsection
