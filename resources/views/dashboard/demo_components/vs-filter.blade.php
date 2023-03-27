@extends('dashboard.layout.index')

@section('stylesheet')
    {{-- <link rel="stylesheet" href="{{ asset('css/dashboard/vs-filter.css') }}"> --}}
@endsection

@section('content')
    <div class="w-75 m-auto py-4">
        <h2 class="text-center">Welcome To <span class="bg-warning text-danger">[ Filter ]</span> Component Demo Page</h2>
    </div>
    <div class="component-use-case-wrapper bg-dark p-3 m-auto" style="width: 80%; height: 80vh;">
        <!-- seach content container -->
        @include('components.vs-filter', [
            'unique_element_id'  => 'color-f-1',
            'module_entity'      => 'Product',
            'filter_by'          => 'name',
            ]
        )
    </div>
@endsection

@section('script')
    <script src="{{ asset('js/components/vs-filter.js') }}"></script>

    <script>
        // Global Var
        const ApiUrl   = "{!! url('api') !!}",
              ApiToken = "{!! csrf_token() !!}";

        $(document).ready(function(){
            // code ...

            filter_component_activateModuleFilter(ApiUrl, ApiToken, 'GET');
            updateColorsFilterData( filter_component_getHiddenInputResponseElement('color-f-1') );
            
            // code ...
        });

        /**
         * Update colors table listing data with filter result 
         * from database based on given input hidden filter element id parameter.
         * @param [String] responseFilterElementId
         * @return [Object_Key_Value] response
         */
        function updateColorsFilterData( responseFilterElementId ){
            const input_hiddenElement = $(responseFilterElementId);

            input_hiddenElement.on('change', function(){
                const State = $(this);

                const Response = {
                    value         : JSON.parse( State.val() ),
                    status        : State.attr('filterStatus'),
                    message       : State.attr('filterMessage'),
                    detailMessage : State.attr('filterDetailMessage'),
                }

                console.log( Response );
                // do the update record based on response ...
            });
        };
    </script>
@endsection