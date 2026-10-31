<!doctype html>
<html lang="ar" dir="rtl">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
@php
    $brandLogoPath = file_exists(public_path('images/Untitled-4.png'))
        ? 'images/Untitled-4.png'
        : (file_exists(public_path('images/imagelogo.webp'))
            ? 'images/imagelogo.webp'
            : 'images/barber-house-mark.png');
    $brandLogoUrl = rtrim((string) config('app.url'), '/') . '/' . ltrim($brandLogoPath, '/');
@endphp
<body style="margin:0;padding:28px 12px;background:#f6f3ef;font-family:Arial,Tahoma,sans-serif;direction:rtl;text-align:right;color:#30231f">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;margin:0 auto;background:#fff;border:1px solid #eadbd1;border-radius:20px;overflow:hidden">
        <tr><td align="center" style="padding:28px 24px 22px;background:#fbf7f3;border-bottom:1px solid #eee3dc">
            <img src="{{ $brandLogoUrl }}" width="76" height="76" alt="شعار باربر هاوس" style="display:block;width:76px;height:76px;object-fit:contain;margin:0 auto 10px">
            <div style="font-size:11px;font-weight:bold;letter-spacing:3px;color:#a47764">BARBER HOUSE</div>
            <div style="margin-top:5px;font-size:18px;font-weight:bold;color:#3a2923">باربر هاوس</div>
        </td></tr>
        <tr><td style="padding:30px 28px;line-height:1.95">
            <h1 style="margin:0 0 14px;text-align:center;color:#4d2d23;font-size:23px">تأكيد طلب تغيير كلمة المرور</h1>
            <p>مرحبًا {{ $accountName }}،</p>
            <p style="color:#665b55">طلب {{ $requesterName }} بدء إجراء تغيير كلمة المرور الخاصة بحسابك في باربر هاوس.</p>
            <div style="margin:20px 0;padding:16px;border:1px solid #ead7bd;border-radius:14px;background:#fff9ef;color:#76512c;line-height:1.9">
                <strong>لن يتم تغيير كلمة المرور دون موافقتك.</strong><br>
                عند الموافقة، سنرسل لك رسالة منفصلة تحتوي على رابط آمن لتعيين كلمة مرور جديدة بنفسك. إذا رفضت، سيظل حسابك كما هو وسيتم إبلاغ مقدم الطلب.
            </div>
            <div style="margin:26px 0;text-align:center"><a href="{{ $approveUrl }}" style="display:inline-block;padding:14px 28px;border-radius:12px;background:#704331;color:#fff;text-decoration:none;font-weight:bold">مراجعة الطلب والرد</a></div>
            <p style="color:#87766d;font-size:13px">ينتهي الطلب في {{ $expiresAt }}. تجاهل الرسالة إذا لم تكن تتوقع هذا الطلب، ولا تشارك الرابط مع أي شخص.</p>
            <p style="margin:24px 0 0;color:#6d5b52">تحياتنا،<br><strong>فريق باربر هاوس</strong></p>
        </td></tr>
        <tr><td align="center" style="padding:16px;background:#f8f4f0;border-top:1px solid #eee3dc;color:#9a8b82;font-size:11px">هذه رسالة تلقائية، برجاء عدم الرد عليها.</td></tr>
    </table>
</body>
</html>
