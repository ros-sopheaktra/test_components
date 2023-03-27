@extends('dashboard.layout.index')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('css/dashboard/vs-checkbox.css') }}">
@endsection

@section('content')
    <div class="w-75 m-auto py-4">
        <h2 class="text-center">Welcome To <span class="bg-warning text-danger">[ Checkbox ]</span> Component Demo Page</h2>
    </div>
    <div class="component-use-case-wrapper bg-dark p-3 m-auto" style="width: 80%; height: 80vh;">
        <!-- checkbox -->
        @include('components.vs-checkbox', [
            'checkbox_label_text'  => 'One',
            'checkbox_label_class' => 'text-white',
            'unique_id'            => 'checkbox-code-001',
        ])
        <!-- checkbox -->
        @include('components.vs-checkbox', [
            'checkbox_label_text'  => 'Two',
            'checkbox_label_class' => 'text-white',
            'unique_id'            => 'checkbox-code-002',
            'checkbox_class'       => 'mt-3 checkboxRadio',
        ])
        <!-- checkbox -->
        @include('components.vs-checkbox', [
            'checkbox_label_text'  => 'Three',
            'checkbox_label_class' => 'text-white',
            'unique_id'            => 'checkbox-code-003',
            'checkbox_class'       => 'checkboxRadio',
        ])
    </div>
@endsection

@section('script')
    <script src="{{ asset('js/components/vs-checkbox.js') }}"></script>

    <script>
        $(document).ready(function(){
            // test checkbox code 001 use-case
            const showAlertCheckbox001 = function() { 
                const checkbox = $(this);
                alert('Hi, Alert from checkbox code 001 [check is '+ checkbox.prop('checked') +']') 
            };
            vsCheckBoxCheck( '#checkbox-code-001', showAlertCheckbox001 );

            // make check like radio
            const checkboxRadio = function() {
                $('.checkboxRadio').not(this).prop('checked', false);
            };
            vsCheckBoxCheck( '.checkboxRadio', checkboxRadio );
        });
    </script>
@endsection