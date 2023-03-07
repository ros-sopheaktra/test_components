@extends('dashboard.layout.index')

{{-- custom stylesheet --}}
@section('stylesheet')
<link rel="stylesheet" href="{{asset('css/dashboard/components/vs-drop-down-pv.css')}}">
{{-- search selecte --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
@endsection

{{-- BEGIN:: Adjustment --}}
@section('content')

<div class="container ">
    <div class="row">
        <div class="col-md-12">
            <form class="mt-5" id="exrate-creation-form" action="#" method="POST" enctype="multipart/form-data">
                @csrf
                <!-- general information -->
                <div class="font-weight-bold header-style border-radius-on"></div>
                <div class="general-info-wrapper shadow-lg border-radius-bottom">
                    <div class="row">
                        <!-- date -->
                        @include('dashboard.components.vs-input',[
                            'classwrapper'  => 'form-group col-md-6',
                            'inputType'     => 'text',
                            'inputValue'    => '',
                            'maxlenght'     => '',
                            'minlangth'     => '',
                            'classInput'    => 'form-control',
                            'nameInput'     => 'pnam',
                            'idInput'       => 'name',
                            'require',
                            ]
                        )
                        @include('dashboard.components.vs-input',[
                            'classwrapper'  => 'form-group col-md-6',
                            'inputType'     => 'date',
                            'inputValue'    => '',
                            'maxlenght'     => '',
                            'minlangth'     => '',
                            'classInput'    => 'form-control',
                            'nameInput'     => 'pnam',
                            'idInput'       => 'name',
                            'require',
                            ]
                        )
                </div>
                <!-- add & reset button -->
                <div class="exrate-btn-wrapper row float-right mt-3">
                    <div class="col-md-12">
                        @include('dashboard.components.vs-button', [
                            'btn_type'        => 'submit',
                            'btn_unique_id'   => 'btn-1',
                            'btn_style_class' => 'btn border-radius-md btn-submit btn-sm btn-outline-dark mr-4  pr-5',
                            'btn_text'        => 'Submit',
                            'attr'            => "",
                            'dev_class'       => "btn border-radius-md btn-submit",
                            ]
                        )
                        <a href="#" class="btn btn-sm border-radius-md btn-outline-danger pl-5 pr-5">Reset</a>
                    </div>
                </div>
            </form>
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
    <script src="{{ asset('js/dashboard/components/vs-input.js') }}"></script>
    <script>
        $(document).ready(function(){
            buttonOnClick('#btn-1', showAlert);
        });

        function showAlert(){
            alert('hi');
        }
     
    </script>
@endsection