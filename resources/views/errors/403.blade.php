<x-admin-layout>
    <div class="content d-flex justify-content-center align-items-center">

        <!-- Container -->
        <div class="flex-fill">

            <!-- Error title -->
            <div class="mb-4 text-center">
                <img src="{{ asset('assets/images/403.svg') }}" class="mb-3 img-fluid img-error" height="80" alt="">
                <h6 class="w-md-25 mx-md-auto">Rất tiếc, đã có lỗi xảy ra.<br> Bạn không có quyền truy cập tài nguyên này trên máy chủ.</h6>
            </div>
            <!-- /error title -->


            <!-- Error content -->
            <div class="text-center">
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    <i class="ph-house me-2"></i>
                    Trở về bảng điều khiển
                </a>
            </div>
            <!-- /error wrapper -->

        </div>
        <!-- /container -->

    </div>
</x-admin-layout>
