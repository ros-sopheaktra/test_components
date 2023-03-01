@extends('dashboard.layout.index')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('css/dashboard/vs-button.css') }}">
@endsection

@section('content')
    <div class="w-75 m-auto py-4">
        <h2 class="text-center">Welcome To <span class="bg-warning text-danger">[ Drop Down ]</span> Component Demo Page</h2>
    </div>
    <div class="component-use-case-wrapper bg-dark p-3 m-auto" style="width: 80%; height: 80vh;">
        <!-- simple component that alert any value after selected ( on change deteched ) -->
        @include('components.vs-dropdown', [
            'unique_id'  => 'select-code-001',
            'prompt'     => 'select month',
            'selectType' => 'static',
        ])

        <!-- simple component that alert any value after selected ( on change deteched ) -->
        @include('components.vs-dropdown', [
            'unique_id'  => 'select-code-002',
            'prompt'     => 'select currency',
            'selectType' => 'static',
        ])

        <!-- dynamic selection -->
        @include('components.vs-dropdown', [
            'component_wrapper_class' => 'mt-3',
            'unique_id'               => 'select-code-003',
            'prompt'                  => 'select product varaint',
            'selectType'              => 'dynamic',
        ])
        <input type="hidden" id="productVariants" value="{{$productVariantsCollections}}">
    </div>
@endsection

@section('script')
    <script src="{{ asset('js/components/vs-dropdown.js') }}"></script>
    <script src="{{ asset('js/enum/months.js') }}"></script>
    <script src="{{ asset('js/enum/currencies.js') }}"></script>
    <script src="{{ asset('js/utils/text_manippulations.js') }}"></script>

    <script>
        $(document).ready(function(){
            const months = getMonths();
            generate_selection_options( 'select-code-001', months );

            const currencies = getCurrencies();
            generate_selection_options( 'select-code-002', currencies );

            const productVariants = JSON.parse($('#productVariants').val());
            generate_selection_options( 'select-code-003', productVariants );
        });
    </script>
@endsection
