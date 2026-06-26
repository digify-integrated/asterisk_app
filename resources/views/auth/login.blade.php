@extends('layouts.auth')

@section('content')
    <form class="form w-100" id="login_form" method="POST" action="#" novalidate>
        @csrf

        <div class="fv-row mb-6">
            <label class="form-label fs-7 fw-semibold text-gray-700 mb-2">Email address</label>

            <input type="email" name="email" id="email" class="form-control bg-transparent" autocomplete="off" placeholder="you@company.com"/>
        </div>

        <div class="fv-row mb-5">
            <label class="form-label fs-7 fw-semibold text-gray-700 mb-2">Password</label>

            <div class="input-group">
                <input type="password" id="password" name="password" class="form-control bg-transparent" placeholder="Enter your password">

                <span class="input-group-text bg-transparent cursor-pointer password-addon">
                    <i class="ki-outline ki-eye fs-3"></i>
                </span>
            </div>
        </div>

        <div class="d-grid mb-6">
            <button type="submit" id="signin" class="btn btn-primary fw-semibold">
                Sign in
            </button>
        </div>
    </form>
@endsection

@push('scripts')
    <script type="module" src="{{ asset('assets/js/auth/login.js') }}"></script>
@endpush