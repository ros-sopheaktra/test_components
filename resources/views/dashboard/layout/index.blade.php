<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <!-- Heading Content Block -->
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        
        <!-- CSRF TOKEN -->
        <meta name="csrf-token" content="{{ csrf_token() }}">


        <!-- font awesome icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- custom stylesheet -->
        @yield('stylesheet')

        <!--  -->
    </head>
    <!-- Body Content Block -->
    <body id="page-top mb-5">
        {{-- content --}}
        @yield('content')
    </body>
</html>

<!-- BEGIN:: custom script -->
    <!-- vendors script -->
    <script src="{{ asset('js/dashboard/vendors/jquery.min.js') }}"></script>
    
    <!-- custom script -->
    @yield('script')

<!-- END::   custom script -->