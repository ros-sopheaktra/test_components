<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Document</title>
</head>
<body>
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
                    ]
                )

                <button class="mt-3">Test BTN</button>
              </form>
        </div>
       </div>
    </div>
</body>
</html>

{{-- script custom --}}
<script src="{{ asset('js/dashboard/vendors/jquery.min.js') }}"></script>
<script src="{{ asset('js/dashboard/components/vs-button.js') }}"></script>
<script>
  $(document).ready(function(){
    buttonOnClick('#btn-1', showAlert.call());
    // buttonOnClickTest('#btn-1');
  });

  function showAlert(){
    alert('hi');
  }
</script>