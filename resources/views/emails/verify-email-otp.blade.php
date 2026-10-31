<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>تأكيد البريد الإلكتروني</title>
</head>

@php
    $brandLogoPath = file_exists(public_path('images/Untitled-4.png'))
        ? 'images/Untitled-4.png'
        : (file_exists(public_path('images/imagelogo.webp'))
            ? 'images/imagelogo.webp'
            : 'images/barber-house-mark.png');
    $brandLogoUrl = rtrim((string) config('app.url'), '/') . '/' . ltrim($brandLogoPath, '/');
@endphp
<body style="margin:0; padding:0; background:#f6f3ef; font-family:Arial, Helvetica, sans-serif;">

    <table
        width="100%"
        cellpadding="0"
        cellspacing="0"
        border="0"
        style="background:#f6f3ef; padding:40px 15px;"
    >
        <tr>
            <td align="center">

                <table
                    width="100%"
                    cellpadding="0"
                    cellspacing="0"
                    border="0"
                    style="
                        max-width:600px;
                        background:#ffffff;
                        border:1px solid #eadbd1;
                        border-radius:20px;
                        overflow:hidden;
                        box-shadow:0 14px 40px rgba(48,30,25,.08);
                    "
                >

                    {{-- Header --}}
                    <tr>
                        <td
                            style="
                                background:#fbf7f3;
                                padding:28px 25px 24px;
                                text-align:center;
                                border-bottom:1px solid #eee3dc;
                            "
                        >

                            <img
                                src="{{ $brandLogoUrl }}"
                                width="76"
                                height="76"
                                alt="شعار باربر هاوس"
                                style="display:block;width:76px;height:76px;object-fit:contain;margin:0 auto 10px"
                            >
                            <div style="font-size:11px;font-weight:bold;letter-spacing:3px;color:#a47764;margin-bottom:6px">BARBER HOUSE</div>
                            <div style="font-size:18px;font-weight:bold;color:#3a2923;margin-bottom:12px">باربر هاوس</div>

                            <h1
                                style="
                                    margin:0;
                                    color:#4d2d23;
                                    font-size:24px;
                                    font-weight:700;
                                "
                            >
                                تأكيد البريد الإلكتروني
                            </h1>

                            <p
                                style="
                                    margin:10px 0 0;
                                    color:#817168;
                                    font-size:14px;
                                "
                            >
                                خطوة واحدة وتقدر تكمل إنشاء حسابك
                            </p>

                        </td>
                    </tr>


                    {{-- Content --}}
                    <tr>
                        <td style="padding:40px 35px 25px;">

                            <h2
                                style="
                                    margin:0 0 12px;
                                    color:#111827;
                                    font-size:21px;
                                    text-align:center;
                                "
                            >
                                رمز التحقق الخاص بك
                            </h2>

                            <p
                                style="
                                    margin:0;
                                    color:#64748b;
                                    font-size:15px;
                                    line-height:1.8;
                                    text-align:center;
                                "
                            >
                                استخدم الرمز التالي لتأكيد بريدك الإلكتروني.
                                <br>
                                الرمز صالح لمدة <strong>5 دقائق</strong>.
                            </p>


                            {{-- OTP --}}
                            <div
                                style="
                                    margin:30px auto;
                                    padding:18px 20px;
                                    background:#fcf8f5;
                                    border:1px solid #eadbd1;
                                    border-radius:16px;
                                    text-align:center;
                                "
                            >

                                <div
                                    style="
                                        color:#a47764;
                                        font-size:12px;
                                        font-weight:700;
                                        margin-bottom:8px;
                                    "
                                >
                                    رمز التحقق
                                </div>

                                <div
                                    style="
                                        color:#4d2d23;
                                        font-size:34px;
                                        line-height:1;
                                        font-weight:800;
                                        letter-spacing:10px;
                                        direction:ltr;
                                    "
                                >
                                    {{ $otp }}
                                </div>

                            </div>


                            {{-- Security --}}
                            <div
                                style="
                                    padding:15px 16px;
                                    background:#fcf8f5;
                                    border-radius:12px;
                                    color:#64748b;
                                    font-size:13px;
                                    line-height:1.8;
                                    text-align:right;
                                "
                            >
                                <strong style="color:#51413a;">
                                    ملاحظة أمنية:
                                </strong>
                                لا تشارك رمز التحقق مع أي شخص.
                                فريق الدعم لن يطلب منك هذا الرمز.
                            </div>


                            <p
                                style="
                                    margin:28px 0 0;
                                    color:#94a3b8;
                                    font-size:12px;
                                    line-height:1.8;
                                    text-align:center;
                                "
                            >
                                لو أنت لم تطلب إنشاء حساب، تجاهل الرسالة.
                            </p>

                        </td>
                    </tr>


                    {{-- Footer --}}
                    <tr>
                        <td
                            style="
                                padding:20px 25px;
                                background:#fcf8f5;
                                border-top:1px solid #eee3dc;
                                text-align:center;
                            "
                        >

                            <p
                                style="
                                    margin:0;
                                    color:#64748b;
                                    font-size:12px;
                                "
                            >
                                {{ config('app.name') }}
                            </p>

                            <p
                                style="
                                    margin:5px 0 0;
                                    color:#94a3b8;
                                    font-size:11px;
                                "
                            >
                                هذه رسالة تلقائية، برجاء عدم الرد عليها.
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>