
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="theme-color" content="#f8fafc">

    <title>تأكيد البريد الإلكتروني | باربر هاوس</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(180, 134, 109, .15),
                    transparent 35%
                ),
                #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .verify-wrapper {
            width: 100%;
            max-width: 460px;
        }

        .verify-card {
            width: 100%;
            background: #ffffff;
            border-radius: 24px;
            padding: clamp(22px, 6vw, 38px);
            box-shadow: 0 20px 50px rgba(15, 23, 42, .10);
            border: 1px solid #e2e8f0;
        }

        .brand {
            text-align: center;
            margin-bottom: 28px;
        }

        .brand-logo {
            display: block;
            width: 82px;
            height: 82px;
            margin: 0 auto 12px;
            object-fit: contain;
            filter: drop-shadow(0 10px 18px rgba(68, 42, 31, .13));
        }

        .brand-kicker {
            display: block;
            margin-bottom: 10px;
            color: #a47764;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .22em;
        }

        .brand h1 {
            margin: 0;
            color: #111827;
            font-size: clamp(21px, 5vw, 26px);
        }

        .brand p {
            margin: 12px 0 0;
            color: #64748b;
            font-size: 14px;
            line-height: 1.9;
        }

        .email {
            display: inline-block;
            max-width: 100%;
            margin-top: 8px;
            color: #92400e;
            font-weight: 700;
            direction: ltr;
            unicode-bidi: plaintext;
            overflow-wrap: anywhere;
        }

        .alert {
            padding: 13px 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.7;
        }

        .alert-success {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .otp-label {
            display: block;
            margin-bottom: 14px;
            color: #334155;
            font-size: 14px;
            font-weight: 700;
        }

        .otp-container {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            direction: ltr;
            gap: clamp(5px, 2vw, 10px);
            max-width: 390px;
            margin: 0 auto 12px;
        }

        .otp-input {
            width: 100%;
            min-width: 0;
            height: clamp(46px, 13vw, 58px);
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            background: #f8fafc;
            text-align: center;
            font-size: clamp(19px, 5vw, 24px);
            font-weight: 700;
            color: #111827;
            outline: none;
            transition: .2s;
        }

        .otp-input:focus {
            background: #fff;
            border-color: #b9866d;
            box-shadow: 0 0 0 4px rgba(185, 134, 109, .15);
        }

        .otp-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }

        .verify-button {
            width: 100%;
            border: 0;
            border-radius: 14px;
            padding: 14px 18px;
            background: linear-gradient(135deg, #92400e, #b9866d);
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 18px;
            transition: .2s;
        }

        .verify-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(146, 64, 14, .2);
        }

        .resend-section {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .resend-text {
            color: #64748b;
            font-size: 13px;
            line-height: 1.8;
        }

        .timer {
            color: #92400e;
            font-weight: 700;
        }

        .resend-button {
            border: 0;
            background: transparent;
            color: #92400e;
            font-weight: 700;
            cursor: pointer;
            font-size: 14px;
        }

        .resend-button:hover {
            color: #78350f;
        }

        .back-link {
            display: block;
            margin-top: 22px;
            text-align: center;
            text-decoration: none;
            color: #64748b;
            font-size: 13px;
        }

        .back-link:hover {
            color: #92400e;
        }

        @media (max-width: 360px) {

            .verify-card {
                padding: 22px 14px;
            }

            .otp-container {
                gap: 5px;
            }

            .otp-input {
                border-radius: 9px;
            }

        }

    </style>

</head>

<body>

@php
    $oldOtp = (string) old('otp', '');
    $brandLogoPath = file_exists(public_path('images/logo4-removebg-preview.png'))
        ? 'images/logo4-removebg-preview.png'
        : (file_exists(public_path('images/logo4-removebg-preview.png'))
            ? 'images/logo4-removebg-preview.png'
            : 'images/logo4-removebg-preview.png');
@endphp

<div class="verify-wrapper">

    <div class="verify-card">

        {{-- Header --}}
        <div class="brand">

            <img
                src="{{ asset($brandLogoPath) }}"
                alt="شعار باربر هاوس"
                class="brand-logo"
                width="82"
                height="82"
            >
            <span class="brand-kicker">BARBER HOUSE</span>

            <h1>
                تأكيد البريد الإلكتروني
            </h1>

            <p>
                أدخل رمز التحقق المرسل إلى بريدك الإلكتروني

                <br>

                <span class="email">
                    {{ $email }}
                </span>
            </p>

        </div>

        {{-- Success Message --}}
        @if(session('success'))

            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>

        @endif

        {{-- Error Message --}}
        @if($errors->has('otp'))

            <div class="alert alert-error" role="alert">
                {{ $errors->first('otp') }}
            </div>

        @endif

        {{-- Verification Form --}}
        <form
            method="POST"
            action="{{ route('verification.verify') }}"
            id="verify-form"
        >

            @csrf

            <input
                type="hidden"
                name="email"
                value="{{ $email }}"
            >

            <input
                type="hidden"
                name="otp"
                id="otp-hidden"
                value="{{ $oldOtp }}"
            >

            <label class="otp-label" for="otp-1">
                رمز التحقق
            </label>

            <div class="otp-container" aria-label="خانات رمز التحقق">

                @for($i = 0; $i < 6; $i++)

                    <input
                        id="otp-{{ $i + 1 }}"
                        type="text"
                        maxlength="1"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}"
                        class="otp-input"
                        value="{{ $oldOtp[$i] ?? '' }}"
                        aria-label="الرقم {{ $i + 1 }}"
                        @if($i === 0) autofocus @endif
                    >

                @endfor

            </div>

            <button
                type="submit"
                class="verify-button"
            >
                تأكيد البريد الإلكتروني
            </button>

        </form>

        {{-- Resend OTP --}}
        <div class="resend-section">

            <div
                class="resend-text"
                id="resend-message"
                aria-live="polite"
            >
                يمكنك طلب رمز جديد بعد

                <span class="timer" id="timer">
                    ٦٠
                </span>

                ثانية
            </div>

            <form
                method="POST"
                action="{{ route('verification.resend') }}"
                id="resend-form"
                style="display: none;"
            >

                @csrf

                <input
                    type="hidden"
                    name="email"
                    value="{{ $email }}"
                >

                <button
                    type="submit"
                    class="resend-button"
                >
                    لم يصلك الرمز؟ إرسال رمز جديد
                </button>

            </form>

        </div>

        {{-- Back Link --}}
        <a
            href="{{ route('register') }}"
            class="back-link"
        >
            العودة إلى صفحة التسجيل ←
        </a>

    </div>

</div>

<script>

    const inputs = document.querySelectorAll('.otp-input');

    const hiddenInput = document.getElementById('otp-hidden');

    const form = document.getElementById('verify-form');

    /*
    |--------------------------------------------------------------------------
    | OTP Helper
    |--------------------------------------------------------------------------
    */

    function updateOtp() {

        let otp = '';

        inputs.forEach(input => {
            otp += input.value;
        });

        hiddenInput.value = otp;

    }

    /*
    |--------------------------------------------------------------------------
    | Handle OTP Input
    |--------------------------------------------------------------------------
    */

    function fillOtp(startIndex, value) {

        const digits = value
            .replace(/\D/g, '')
            .slice(0, inputs.length - startIndex);

        digits.split('').forEach((digit, index) => {

            if (inputs[startIndex + index]) {
                inputs[startIndex + index].value = digit;
            }

        });

        updateOtp();

        if (digits.length > 0) {

            const lastIndex = Math.min(
                startIndex + digits.length - 1,
                inputs.length - 1
            );

            inputs[lastIndex].focus();

        }

    }

    inputs.forEach((input, index) => {

        input.addEventListener('input', function () {

            const value = this.value.replace(/\D/g, '');

            if (value.length > 1) {

                this.value = '';

                fillOtp(index, value);

                return;

            }

            this.value = value.slice(0, 1);

            if (this.value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }

            updateOtp();

        });

        /*
        |--------------------------------------------------------------------------
        | Backspace
        |--------------------------------------------------------------------------
        */

        input.addEventListener('keydown', function (event) {

            if (
                event.key === 'Backspace' &&
                !this.value &&
                index > 0
            ) {

                inputs[index - 1].value = '';

                inputs[index - 1].focus();

                updateOtp();

            }

        });

        /*
        |--------------------------------------------------------------------------
        | Paste OTP
        |--------------------------------------------------------------------------
        */

        input.addEventListener('paste', function (event) {

            event.preventDefault();

            const pasted = (
                event.clipboardData || window.clipboardData
            )
                .getData('text')
                .replace(/\D/g, '');

            fillOtp(index, pasted);

        });

    });

    /*
    |--------------------------------------------------------------------------
    | Form Submit
    |--------------------------------------------------------------------------
    */

    form.addEventListener('submit', function () {
        updateOtp();
    });

    // Restore hidden OTP value on page load
    updateOtp();

    /*
    |--------------------------------------------------------------------------
    | Resend Countdown
    |--------------------------------------------------------------------------
    */

    let seconds = 60;

    const timer = document.getElementById('timer');

    const resendMessage = document.getElementById('resend-message');

    const resendForm = document.getElementById('resend-form');

    const interval = setInterval(() => {

        seconds--;

        timer.textContent = new Intl.NumberFormat('ar-EG').format(seconds);

        if (seconds <= 0) {

            clearInterval(interval);

            resendMessage.style.display = 'none';

            resendForm.style.display = 'block';

        }

    }, 1000);

</script>

</body>

</html>
