<!DOCTYPE html>
<html lang="en">
<head>
    <title> {{ $pageTitle ?? env('APP_NAME', 'Asterisk') }} </title>
    @include('partials.required-css')
</head>
<body id="kt_body" class="auth-bg">
    @include('partials.theme-switcher')

    <div class="d-flex flex-column flex-root min-vh-100">
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
           <div class="d-flex flex-lg-row-fluid w-lg-60 position-relative overflow-hidden hero-section">

                <div class="hero-bg"></div>

                <div class="d-flex flex-column justify-content-center px-10 px-lg-15 py-15 w-100 position-relative z-1">
                    <div class="mb-12">
                        <a href="/" class="d-inline-flex align-items-center">
                            <img src="/assets/media/logos/logo-light.svg" class="h-45px" alt="Asterisk Solutions">
                        </a>
                    </div>

                    <div class="mb-6">
                        <span class="badge badge-primary px-4 py-2">Technology • Creativity • Growth</span>
                    </div>

                    <div class="mb-10">
                        <h1 class="fw-bolder fs-2qx lh-sm mb-6 text-white">Building digital solutions<br> that help businesses grow.</h1>

                        <p class="text-muted fs-5 mw-550px">
                            Asterisk Solutions delivers custom software, business automation,
                            and modern web applications designed to improve efficiency,
                            simplify workflows, and strengthen your digital presence.
                        </p>
                    </div>

                    <div class="d-flex flex-column gap-6 mb-12">
                        <div class="d-flex align-items-start">
                            <i class="ki-duotone ki-check-circle fs-2 text-primary me-3 mt-1"></i>

                            <div>
                                <div class="fw-bold text-white">
                                    Custom Software Development
                                </div>
                                <div class="text-muted fs-7">
                                    Tailored web applications built around your business processes.
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-start">
                            <i class="ki-duotone ki-check-circle fs-2 text-primary me-3 mt-1"></i>

                            <div>
                                <div class="fw-bold text-white">
                                    Business Process Automation
                                </div>
                                <div class="text-muted fs-7">
                                    Reduce manual work and improve productivity with smart automation.
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-start">
                            <i class="ki-duotone ki-check-circle fs-2 text-primary me-3 mt-1"></i>

                            <div>
                                <div class="fw-bold text-white">
                                    Reliable Technical Partnership
                                </div>
                                <div class="text-muted fs-7">
                                    From consultation to deployment and ongoing support, we're with you every step of the way.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="text-muted fs-7 mb-3">
                            Helping businesses of every size
                        </div>

                        <div class="d-flex flex-wrap gap-4">
                            <span class="badge badge-dark">Startups</span>
                            <span class="badge badge-dark">MSMEs</span>
                            <span class="badge badge-dark">Enterprises</span>
                            <span class="badge badge-dark">Government</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column flex-lg-row-fluid w-lg-40 bg-body">
                <div class="d-flex flex-column justify-content-center flex-column-fluid px-8 px-lg-16 py-12">
                    <div class="mx-auto w-100" style="max-width: 500px;">
                        <div class="mb-10 text-center">
                            <h1 class="fw-bolder text-gray-900 fs-2hx mb-3">{{$title}}</h1>
                            <p class="text-gray-500 fs-5">{{ $description }}</p>
                        </div>
                        
                        @yield('content')

                        <div class="text-center mt-12">
                            <span class="text-gray-500 fs-7">&copy; {{ date('Y') }} Asterisk Solutions</span>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    @include('partials.error-modal')

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    
    @stack('scripts')
</body>
</html>