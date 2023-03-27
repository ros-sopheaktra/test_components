@extends('dashboard.layout.index')

@section('stylesheet')
    <link rel="stylesheet" href="{{ asset('css/dashboard/vs-button.css') }}">
@endsection

@section('content')
    <div class="w-75 m-auto py-4">
        <h2 class="text-center">Welcome To <span class="bg-warning text-danger">[ Button ]</span> Component Demo Page</h2>
    </div>
    <div class="component-use-case-wrapper bg-dark p-3 m-auto" style="width: 80%; height: 80vh;">
        <!-- simple component that alert custom text on clicked -->
        @include('components.vs-button', [
            'button_type'  => 'button',
            'button_class' => 'mb-3',
            'unique_id'    => 'button-code-001',
            'button_text'  => 'Demo Code 001',
        ])

        <!-- simple component that change color and text on each clicked -->
        @include('components.vs-button', [
            'button_type'  => 'button',
            'unique_id'    => 'button-code-002',
            'button_text'  => 'white button with black text',
            'attributes'   => "activeColor=white",
        ])

        <!-- test difference usecase of button component mix together with form -->
        <form action="#" class="w-50 mt-5 bg-light p-5 rounded">
            <p>Simple Form: </p>
            <hr>
            <div>
                <label for="name">Username: </label>
                <input type="text" id="nane" name="name">
            </div>
            <div>
                <label for="password">Password: </label>
                <input type="password" id="passowrd" name="password">
            </div>
            <div class="d-flex justify-content-between mt-3" style="width: 8rem;">
                <!-- simple button component for submit -->
                @include('components.vs-button', [
                    'button_type'  => 'reset',
                    'unique_id'    => 'button-code-form-reset',
                    'button_text'  => 'Reset',
                    'button_class' => 'btn-sm btn-outline-warning',
                ])
                <!-- simple button component for submit -->
                @include('components.vs-button', [
                    'button_type'  => 'submit',
                    'unique_id'    => 'button-code-form-submit',
                    'button_text'  => 'Submit',
                    'button_class' => 'btn-sm btn-outline-success',
                ])
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script src="{{ asset('js/components/vs-button.js') }}"></script>

    <script>
        $(document).ready(function(){
            // test button code 001 use-case
            const showAlertBtn001 = () => { alert('Hi, Alert from button code 001') };
            vsButtonOnClick( 'button-code-001', showAlertBtn001 );

            // toggle white to green button on each clicked
            const toggleBtn002Colors = function() {
                const state       = $(this),
                      activeColor = state.attr('activeColor');

                const toggleData = {
                    white: {
                        className   : 'btn bg-success',
                        buttonText  : 'green button with white text',
                        activeColor : 'green',
                    },
                    green : {
                        className   : 'btn bg-light',
                        buttonText  : 'white button with white text',
                        activeColor : 'white',
                    },
                };

                state.text( toggleData[activeColor].buttonText );
                state.attr( 'class', toggleData[activeColor].className );
                state.attr( 'activeColor', toggleData[activeColor].activeColor );
            };
            vsButtonOnClick( 'button-code-002', toggleBtn002Colors );

            // validate mandatory fields before process submit
            const formValidation = function() {
                // some validation function here ...

                // assume that validation are passed
                alert('The form will be submit to backend!');
            };
            vsButtonOnClick( 'button-code-form-submit', formValidation );
        });
    </script>
@endsection
