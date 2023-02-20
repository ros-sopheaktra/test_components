@extends('dashboard.layout.index')

{{-- custom stylesheet --}}
@section('stylesheet')
<link rel="stylesheet" href="{{asset('css/dashboard/vs-button.css')}}">
@endsection

{{-- BEGIN:: Adjustment --}}
@section('content')
<div class="controller mt-5">
  <div class="row">
   <div class="col-md-6 mx-auto">
       <form>
            <div class="form-group">
                <label for="exampleInputEmail1">Email address</label>
                <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
                <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Password</label>
                <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                <label class="form-check-label" for="exampleCheck1">Check me out</label>
            </div>
            {{-- btn component --}}
            @include('dashboard.components.vs-button', [
                'btn_type'        => 'btn',
                'btn_unique_id'   => 'btn-1',
                'btn_style_class' => 'btn-primary',
                'btn_text'        => 'Submit',
                'attr'            => "data-id=0 data-name=$hello",
                'dev_class'       => "",
                ]
            )
            </form>
   </div>
  </div>
</div>
@endsection
{{-- ENG:: Adjustment --}}

{{-- custom script --}}
@section('script')
    <script src="{{ asset('js/dashboard/components/vs-button.js') }}"></script>
    <script>
        $(document).ready(function(){
            buttonOnClick('#btn-1', showAlert);
        });

        function showAlert(){
            alert('hi');
        }
    </script>
@endsection