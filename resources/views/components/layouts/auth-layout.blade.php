<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@include('includes.head')

<body>


<!-- Page content -->
<div class="page-content">

    {{--    <!-- Main sidebar -->--}}
    {{--    @include('includes.sidebar')--}}
    {{--    <!-- /main sidebar -->--}}


    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Inner content -->
        <div class="content-inner">

            <!-- Content area -->
            {{ $slot }}
            <!-- /content area -->

            <!-- Footer -->
            @include('includes.footer')
            <!-- /footer -->

        </div>
        <!-- /inner content -->

    </div>
    <!-- /main content -->

</div>
<!-- /page content -->



</body>

</html>
