<!DOCTYPE html>
<html>
<!-- BEGIN: Head-->

@include('layouts.head')
<!-- END: Head-->
@php
    $settings = getSettings();
    $brandLogo = $settings->dashboard_logo ?? $settings->logo ?? null;
    $brandName = $settings->seo_title ?? config('app.name');
    $brandSubtitle = $settings->seo_description ?? 'Sign in to continue';
    $defaultAuthTitle = 'Welcome to ' . config('app.name');
@endphp
<!-- BEGIN: Body-->
<body class="auth-page" style="--auth-accent: {{ $settings->primary_color ?? '#1fa868' }};">
    <main class="auth-page-shell">
        <section class="auth-page-shell__card">
            <div class="auth-brand">
                <div class="auth-brand__mark">
                    @if($brandLogo)
                        <img src="{{ asset($brandLogo) }}" alt="{{ $brandName }}">
                    @else
                        <span>{{ strtoupper(substr($brandName, 0, 1)) }}</span>
                    @endif
                </div>
            </div>

            <div class="auth-page-shell__header">
                <div class="auth-page-shell__badge">@yield('auth-badge', 'Secure access')</div>
                <h1>@yield('auth-title', $defaultAuthTitle)</h1>
                <p>@yield('auth-subtitle', $brandSubtitle)</p>
            </div>

            <div class="auth-page-shell__body">
                @yield('body')
            </div>
        </section>

        @if(!empty($settings->support_link))
            <a class="auth-page-shell__support" href="{{ $settings->support_link }}" target="_blank" rel="noreferrer">
                Need help? Contact support
            </a>
        @endif
    </main>
</body>
@include('layouts.footer')
<script>
    document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            const targetId = this.getAttribute('data-password-toggle');
            const input = document.getElementById(targetId);

            if (!input) {
                return;
            }

            const isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');
            this.textContent = isPassword ? 'Hide' : 'Show';
            this.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
        });
    });
</script>

</html>
