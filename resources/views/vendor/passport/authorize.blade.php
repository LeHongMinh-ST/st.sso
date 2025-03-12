<x-auth-layout>
    <div class="content login-wrapper">
        <div class="card">
            <div class="card-body">
                <div class="row login-row">
                    <div class="col-xl-6">
                        <div class="login-image-wrapper">
                            <img class="login-image" src="{{ asset('assets/images/auth.svg') }}" alt="login">
                            <div class="line"></div>

                        </div>

                    </div>
                    <div class="col-xl-6">
                        <div  class="login-form">
                            <div class="mb-3 text-center">
                                <div class="gap-1 mt-2 mb-4 d-inline-flex align-items-center justify-content-center">
                                    <img src="{{ asset('assets/images/VNUA.png') }}" class="h-64px" alt="">
                                    <img src="{{ asset('assets/images/FITA.png') }}" class="h-64px" alt="">
                                    <img src="{{ asset('assets/images/logoST.jpg') }}" class="h-64px" alt="">
                                </div>
                                <span class="d-block text-muted">Hệ thống ST Single Sign-On</span>
                                <h5 class="mb-0"><i class="ph-shield-check ph"></i> Uỷ quyền yêu cầu</h5>
                            </div>

                            <!-- Introduction -->
                            <p><strong>{{ $client->name }}</strong> đang yêu cầu quyền truy cập tài khoản của bạn.</p>

                            <!-- Scope List -->
                            @if (count($scopes) > 0)
                                <div class="scopes">
                                    <p><strong>This application will be able to:</strong></p>

                                    <ul>
                                        @foreach ($scopes as $scope)
                                            <li>{{ $scope->description }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="buttons d-flex gap-2 justify-content-center">
                                <!-- Authorize Button -->
                                <form method="post" action="{{ route('passport.authorizations.approve') }}">
                                    @csrf

                                    <input type="hidden" name="state" value="{{ $request->state }}">
                                    <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
                                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                                    <button type="submit" class="btn btn-primary btn-approve"> Xác thực</button>
                                </form>

                                <!-- Cancel Button -->
                                <form method="post" action="{{ route('passport.authorizations.deny') }}">
                                    @csrf
                                    @method('DELETE')

                                    <input type="hidden" name="state" value="{{ $request->state }}">
                                    <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
                                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                                    <button class="btn btn-danger">Từ chối</button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-auth-layout>
