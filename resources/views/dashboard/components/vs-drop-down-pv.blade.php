@extends('dashboard.layout.index')

{{-- custom stylesheet --}}
@section('stylesheet')
<link rel="stylesheet" href="{{asset('css/dashboard/vs-drop-down-pv.css')}}">
@endsection

{{-- BEGIN:: Adjustment --}}
@section('content')

@endsection
{{-- ENG:: Adjustment --}}

{{-- custom script --}}
@section('script')
    <script src="{{ asset('js/dashboard/components/vs-drop-down-pv.js') }}"></script>
@endsection