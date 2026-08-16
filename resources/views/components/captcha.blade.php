@php
    $captchaSettings = getSettings()->captcha_settings;
@endphp

@if(isset($captchaSettings['captcha_settings_status']) && $captchaSettings['captcha_settings_status'] == 'yes')
    @if(in_array($captchaSettings['captcha_settings_provider'], ['all','google']))
        @php
            $sitekey = $captchaSettings['google']['RECAPTCHA_SITE_KEY'] ?? '';
        @endphp
        @if(!empty($sitekey))
            @section('CAPTCHA_SITEKEY', $sitekey )
            <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
            <script>
                grecaptcha.ready(function() {
                    grecaptcha.execute('{{ $sitekey }}', {action: 'submit'}).then(function(token) {
                        document.getElementById('g-recaptcha-response').value = token;
                    });
                });
            </script>
        @endif
    @endif
    @if(in_array($captchaSettings['captcha_settings_provider'], ['all','simple']))
        <div class="auth-captcha-field">
            <div class="auth-captcha-field__label">Security question</div>
            <div class="auth-captcha-field__question-row">
                <div class="auth-captcha-field__question">
                    <span class="auth-captcha-field__question-mark">?</span>
                    <span>{{ getCaptchaQuestion() }}</span>
                </div>
                <div class="auth-captcha-field__answer">
                    <input
                        name="_answer"
                        type="number"
                        class="form-control auth-captcha-field__input"
                        placeholder="Answer"
                        inputmode="numeric"
                        autocomplete="off"
                        required
                    >
                </div>
            </div>
        </div>
    @endif
@endif
