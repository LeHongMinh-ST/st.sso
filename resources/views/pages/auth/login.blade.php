<x-auth-layout>
    <x-slot name="custom_js">
        @vite(['resources/js/auth/login.js'])
    </x-slot>
    <div class="content login-wrapper">
        <div class="card">
            <div class="card-body">
                <div class="row login-row">
                    <div class="col-xl-6 d-flex align-items-center">
                        <div class="login-image-wrapper">
                            <img class="login-image" src="{{ asset('assets/images/login.svg') }}" alt="login">
                            <div class="line"></div>
                            <div class="text-center text-muted">
                                Lưu ý: Nếu bạn chưa có tài khoản xin vui lòng liên hệ với quản trị hệ thống hoặc đăng nhập với tài khoản edu để thiết lập tài khoản
                            </div>

                        </div>

                    </div>
                    <div class="col-xl-6">
                        <form action="{{ route('filament.sso.auth.login.store') }}" class="login-form" method="POST">
                            @csrf
                            <div class="mb-3 text-center">
                                <div class="gap-1 mt-2 mb-4 d-inline-flex align-items-center justify-content-center">
                                    <img src="{{ asset('assets/images/VNUA.png') }}" class="h-64px" alt="">
                                    <img src="{{ asset('assets/images/FITA.png') }}" class="h-64px" alt="">
                                    <img src="{{ asset('assets/images/logoST.jpg') }}" class="h-64px" alt="">
                                </div>
                                <span class="d-block text-muted">Khoa công nghệ thông tin - Học viện nông nghiệp Việt Nam</span>
                                <h5 class="mb-0">Hệ thống ST Single Sign-On</h5>
                            </div>

                            @if ($errors->has('message'))
                                <div class="alert alert-danger">
                                    {{ $errors->first('message') }}
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label">Tài khoản/Email</label>
                                <div class="form-control-feedback form-control-feedback-start">
                                    <input type="text" class="form-control" placeholder="Tên tài khoản" name="username" id="username" value="{{ old('username') }}" />
                                    <div class="form-control-feedback-icon">
                                        <i class="ph-user-circle text-muted"></i>
                                    </div>
                                    @error('username')
                                        <label id="error-username" class="validation-error-label" for="username">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mật khẩu</label>
                                <div class="form-control-feedback form-control-feedback-start">
                                    <input type="password" class="form-control" placeholder="•••••••••••" id="password" name="password" value="{{ old('password') }}" />
                                    <div class="form-control-feedback-icon">
                                        <i class="ph-lock text-muted"></i>
                                    </div>
                                    @error('password')
                                        <label id="error-password" class="validation-error-label" for="password">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3 d-flex align-items-center">
                                <label class="form-check">
                                    <input type="checkbox" name="remember" class="form-check-input" value="1">
                                    <span class="form-check-label">Nhớ mật khẩu</span>
                                </label>

                                {{--                                <a href="#" class="ms-auto">Quên mật khẩu</a> --}}
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
                            </div>
                            @error('message')
                                <label id="message-error" class="text-center validation-error-label w-100" for="basic">{{ $message }}</label>
                            @enderror
                            <div class="mb-3 text-center text-muted content-divider">
                                <span class="px-2">Hoặc đăng nhập với</span>
                            </div>
                            <div class="mb-3 text-center">
                                <a href="{{ route('login.microsoft') }}" class="btn btn-outline-primary btn-icon w-100 border-width-2">
                                    <i class="ph-windows-logo primary"></i>
                                    Tài khoản Microsoft
                                </a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-auth-layout>
