<!DOCTYPE html>
<html lang="en">
    <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Error 500</title>
    <!-- plugins:css -->
    {!! Html::style('melody/vendors/iconfonts/font-awesome/css/all.min.css') !!}
    {!! Html::style('melody/vendors/css/vendor.bundle.base.css') !!}
    {!! Html::style('melody/vendors/css/vendor.bundle.addons.css') !!}
    <!-- endinject -->
    <!-- inject:css -->
    {!! Html::style('melody/css/style.css') !!}
    <!-- endinject -->
    <link rel="shortcut icon" href="../../images/favicon.png" />
    </head>

    <body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center text-center error-page bg-warning">
            <div class="row flex-grow">
            <div class="col-lg-7 mx-auto text-white">
                <div class="row align-items-center d-flex flex-row">
                <div class="col-lg-6 text-lg-right pr-lg-4">
                    <h1 class="display-1 mb-0">500</h1>
                </div>
                <div class="col-lg-6 error-page-divider text-lg-left pl-lg-4">
                    <h2>LO SIENTO!</h2>
                    <h3 class="font-weight-light">Error Interno del Servidor!</h3>
                </div>
                </div>
                <div class="row mt-5">
                <div class="col-12 text-center mt-xl-2">
                    <a class="text-white font-weight-medium" href="{{route('home')}}">Volver al panel Principal</a>
                </div>
                </div>
                <div class="row mt-5">
                <div class="col-12 mt-xl-2">
                </div>
                </div>
            </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    {!! Html::script('melody/vendors/js/vendor.bundle.base.js') !!}
    {!! Html::script('melody/vendors/js/vendor.bundle.addons.js') !!}
    <!-- endinject -->
    <!-- inject:js -->
    {!! Html::script('melody/js/off-canvas.js') !!}
    {!! Html::script('melody/js/hoverable-collapse.js') !!}
    {!! Html::script('melody/js/misc.js') !!}
    {!! Html::script('melody/js/settings.js') !!}
    {!! Html::script('melody/js/todolist.js') !!}
    <!-- endinject -->
    </body>
</html>
