<x-admin-layout>
    <x-slot name="header">
        <div class="shadow page-header page-header-light">
            <div class="page-header-content d-lg-flex">
                <div class="d-flex">
                    <h4 class="mb-0 page-title">
                        Bảng điều khiển
                    </h4>

                    <a href="#page_header" class="p-0 border-transparent btn btn-light align-self-center collapsed d-lg-none rounded-pill ms-auto" data-bs-toggle="collapse">
                        <i class="m-1 ph-caret-down collapsible-indicator ph-sm"></i>
                    </a>
                </div>

            </div>

            <div class="page-header-content d-lg-flex border-top">
                <div class="d-flex">
                    <div class="py-2 breadcrumb">
                        <a href="{{ route('dashboard') }}" class="breadcrumb-item"><i class="ph-house"></i></a>
                        <span class="breadcrumb-item active">Bảng điều khiển</span>
                    </div>

                    <a href="#breadcrumb_elements" class="p-0 border-transparent btn btn-light align-self-center collapsed d-lg-none rounded-pill ms-auto" data-bs-toggle="collapse">
                        <i class="m-1 ph-caret-down collapsible-indicator ph-sm"></i>
                    </a>
                </div>

            </div>
        </div>
    </x-slot>


    <div class="content">
        <div class="mb-3">
            <h6 class="mb-0">Xin chào {{ auth()->user()->full_name }} </h6>
            <span class="text-muted">Chúc một ngày làm việc tốt lành!</span>
        </div>
        <div class="row">
            @foreach ($clients as $item)
                <div class="col-lg-3">

                    <!-- Top placement -->
                    <div class="card">
                        <div class="card-img-actions">
                            <img class="card-img-top img-fluid" src="{{ asset('assets/images/default-app.png') }}" alt="">
                            <div class="card-img-actions-overlay card-img-top">
                                <a href="{{ $item->baseRedirectUrl }}" target="_blank" class="btn btn-outline-white border-width-2" data-popup="lightbox">
                                    Truy cập
                                </a>

                            </div>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title text-title" data-bs-popup="tooltip" title="{{ $item->name }}" data-bs-placement="top">{{ $item->name }}</h5>
                            <p class="card-text text-des">
                                {{ $item->description }}
                            </p>
                        </div>


                    </div>
                    <!-- /top placement -->
                </div>
            @endforeach




        </div>
    </div>
</x-admin-layout>
