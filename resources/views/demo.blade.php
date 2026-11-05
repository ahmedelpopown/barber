@php
    // بيانات العرض والتواصل: استبدل القيم قبل النشر.
    $productName = 'Barber Pro';
    $whatsappNumber = '01155991724'; // رقم دولي بدون + أو مسافات.
    $whatsappMessage = rawurlencode('مرحباً، أريد معرفة تفاصيل الحصول على ' . $productName . '.');
    $whatsappUrl = "https://wa.me/{$whatsappNumber}?text={$whatsappMessage}";
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#10100f">
    <meta name="description" content="{{ $productName }} نظام لإدارة صالون الحلاقة: الحجوزات، الموظفون، العملاء، الخدمات، المخزون والمصروفات. استكشف واجهة العميل ولوحة الإدارة.">
    <title>{{ $productName }} | نظام إدارة صالون الحلاقة</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('demo/css/demo.css') }}">
</head>
<body>
    <div class="reading-progress" aria-hidden="true"><span id="readingProgress"></span></div>

    <header class="topbar" id="top">
        <div class="container topbar-inner">
            <a href="#top" class="brand" aria-label="{{ $productName }} - إلى الأعلى">
                <span class="brand-symbol">B<span>.</span></span>
                <span class="brand-text"><strong>{{ $productName }}</strong><small>BARBER MANAGEMENT SYSTEM</small></span>
            </a>
            <button class="nav-toggle" id="navToggle" type="button" aria-label="فتح القائمة" aria-expanded="false" aria-controls="mainNav"><i></i><i></i></button>
            <nav class="main-nav" id="mainNav" aria-label="التنقل الرئيسي">
                <a href="#overview">نظرة عامة</a>
                <a href="#dashboard">لوحة التحكم</a>
                <a href="#modules">الإدارة</a>
                <a href="#customer">تجربة العميل</a>
                <a href="#gallery">الصور</a>
                <a href="#faq">الأسئلة</a>
            </nav>
            <a href="#contact" class="nav-cta">اطلب تفاصيل المشروع <span>↗</span></a>
        </div>
    </header>

    <main>
        <!-- HERO -->
        <section class="hero dark-section" id="overview">
            <div class="hero-grain" aria-hidden="true"></div>
            <div class="hero-glow hero-glow-a" data-parallax="0.11" aria-hidden="true"></div>
            <div class="hero-glow hero-glow-b" data-parallax="-0.07" aria-hidden="true"></div>
            <div class="container hero-layout">
                <div class="hero-copy reveal">
                    <div class="eyebrow eyebrow-light"><span></span> نظام تشغيل وإدارة صالون الحلاقة</div>
                    <p class="hero-overline">YOUR BARBERSHOP. BETTER ORGANIZED.</p>
                    <h1>مش مجرد موقع للحجز.<br><em>نظام يدير تفاصيل شغلك.</em></h1>
                    <p class="hero-lead">من واجهة يتعرّف عليها العميل ويحجز منها، إلى لوحة تحكم تتابع من خلالها الحجوزات والموظفين والخدمات والمخزون والمصروفات.</p>
                    <div class="hero-buttons">
                        <a class="btn btn-gold" href="#contact">اعرف تفاصيل الحصول عليه <span>↗</span></a>
                        <a class="btn btn-outline" href="#video"><span class="play-small">▶</span> شاهد جولة المشروع</a>
                    </div>
                    <div class="hero-trust">
                        <span><b>✓</b> 4 أنواع مستخدمين وصلاحيات</span>
                        <span><b>✓</b> إدارة تشغيلية من مكان واحد</span>
                    </div>
                </div>
                <div class="hero-preview reveal delay-1">
                    <div class="preview-aura"></div>
                    <div class="preview-window tilt-card">
                        <div class="preview-toolbar"><span class="window-dots"><i></i><i></i><i></i></span><span>ADMIN DASHBOARD</span><span class="toolbar-status">PRODUCT PREVIEW</span></div>
                        <div class="media-frame hero-shot">
                            <img class="asset-img" src="{{ asset('demo/images/hero-dashboard.png') }}" alt="صورة رئيسية من لوحة تحكم Barber Pro" fetchpriority="high">
                            <div class="asset-placeholder"><small>MAIN DASHBOARD SCREENSHOT</small><strong>ضع صورة لوحة التحكم الرئيسية هنا</strong><code>hero-dashboard.webp</code></div>
                        </div>
                    </div>
                    <div class="float-chip chip-one" data-parallax="0.18"><span class="chip-icon">▦</span><span><b>الحجوزات والدور</b><small>متابعة وتنظيم</small></span></div>
                    <div class="float-chip chip-two" data-parallax="-0.13"><span class="chip-live"></span><span><b>إدارة متكاملة</b><small>فريق · خدمات · موارد</small></span></div>
                    <span class="hero-counter">01 <i>/ 08</i></span>
                </div>
            </div>
            <a class="scroll-hint" href="#highlights"><span></span> اكتشف النظام</a>
        </section>

        <!-- PRODUCT HIGHLIGHTS -->
        <section class="highlight-strip" id="highlights">
            <div class="container highlight-grid">
                <article><b>01</b><span><strong>الحجوزات ونظام الدور</strong><small>من طلب الموعد إلى متابعة حالة الحجز</small></span></article>
                <article><b>02</b><span><strong>فريق العمل والعملاء</strong><small>حسابات وأدوار وصلاحيات منفصلة</small></span></article>
                <article><b>03</b><span><strong>الموارد والمصروفات</strong><small>متابعة المخزون وتسجيل مصروفات المحل</small></span></article>
            </div>
        </section>

        <!-- VIDEO -->
        <section class="section video-section" id="video">
            <div class="container">
                <div class="section-heading centered reveal">
                    <span class="eyebrow"><span></span> شوف المنتج أثناء الاستخدام</span>
                    <h2>جولة واحدة داخل النظام،<br><em>من واجهة العميل إلى لوحة الإدارة.</em></h2>
                    <p>الفيديو الكامل هنا هيعرض التنقل بين الصفحات والعمليات الأساسية بدل ما تكتفي بصور ثابتة.</p>
                </div>
                <div class="video-box reveal">
                    <video id="productVideo" controls playsinline preload="metadata" poster="{{ asset('demo/images/video-poster.webp') }}" aria-label="شرح كامل لنظام إدارة صالون الحلاقة">
                        <source src="{{ asset('demo/videos/project-overview.mp4') }}" type="video/mp4">
                        متصفحك لا يدعم تشغيل الفيديو.
                    </video>
                    <div class="video-empty" id="videoEmpty"><span>▶</span><strong>فيديو شرح المشروع بالكامل</strong><small>أضف الملف: public/demo/videos/project-overview.mp4</small></div>
                    <div class="video-meta"><span>FULL PRODUCT WALKTHROUGH</span><span>PUBLIC WEBSITE + ADMIN PANEL</span></div>
                </div>
            </div>
        </section>

        <!-- DASHBOARD -->
        <section class="section dashboard-section soft-section" id="dashboard">
            <div class="container split-layout">
                <div class="section-copy reveal">
                    <span class="eyebrow"><span></span> نظرة المدير على الشغل</span>
                    <label class="section-index">01 / ADMIN DASHBOARD</label>
                    <h2>كل المؤشرات المهمة،<br><em>في مكان واحد.</em></h2>
                    <p>لوحة التحكم بتعرض ملخصًا تشغيليًا يساعد المدير يعرف حالة اليوم والشهر من غير ما يفتح كل قسم على حدة.</p>
                    <ul class="check-list">
                        <li><b>✓</b><span><strong>الحجوزات اليومية</strong><small>إجمالي حجوزات اليوم والمعلّق والمكتمل.</small></span></li>
                        <li><b>✓</b><span><strong>العملاء والموظفون والخدمات</strong><small>أعداد العملاء والموظفين النشطين والخدمات.</small></span></li>
                        <li><b>✓</b><span><strong>المصروفات والمخزون</strong><small>مصروفات الشهر، قيمة المخزون، والأصناف منخفضة الكمية.</small></span></li>
                    </ul>
                    <p class="copy-note">وتعرض اللوحة أحدث الحجوزات والمصروفات والأصناف التي تحتاج متابعة. هي لوحة تشغيلية وليست نظام تقارير ورسوم بيانية متقدم.</p>
                    <a class="inline-link" href="#gallery">شاهد لقطات لوحة التحكم <span>←</span></a>
                </div>
                <div class="section-visual reveal delay-1">
                    <div class="media-frame dashboard-shot tilt-card">
                        <img class="asset-img" src="{{ asset('demo/images/dashboard-overview.png') }}" alt="ملخص مؤشرات لوحة تحكم المدير" loading="lazy">
                        <div class="asset-placeholder"><small>DASHBOARD SCREENSHOT</small><strong>صورة ملخص لوحة التحكم</strong><code>dashboard-overview.webp</code></div>
                    </div>
                    <div class="image-footnote"><span>ADMIN OVERVIEW</span><span>DAILY OPERATIONS</span></div>
                </div>
            </div>
        </section>

        <!-- BOOKING WORKFLOW -->
        <section class="section workflow-section">
            <div class="container">
                <div class="section-heading centered reveal">
                    <span class="eyebrow"><span></span> دورة الحجز ونظام الدور</span>
                    <h2>الحجز مش مجرد فورم،<br><em>دي دورة عمل كاملة.</em></h2>
                    <p>النظام يربط الخدمة والموظف والوقت بالتحقق من التعارض ثم متابعة حالة الحجز وترتيب دور اليوم.</p>
                </div>
                <div class="workflow-steps">
                    <article class="workflow-step reveal"><span>01</span><div class="step-icon">✂</div><h3>اختيار الخدمة</h3><p>اختيار خدمة أو أكثر من الخدمات المتاحة.</p></article>
                    <article class="workflow-step reveal delay-1"><span>02</span><div class="step-icon">♙</div><h3>اختيار الموظف والوقت</h3><p>تحديد الموظف والتاريخ والوقت ضمن مسار الحجز.</p></article>
                    <article class="workflow-step reveal delay-2"><span>03</span><div class="step-icon">◷</div><h3>التحقق من التعارض</h3><p>منع تعارض حجز العميل أو الموظف في نفس التوقيت.</p></article>
                    <article class="workflow-step reveal delay-3"><span>04</span><div class="step-icon">№</div><h3>متابعة الدور والحالة</h3><p>إنشاء الحجز ومتابعة حالته وترتيب أرقام دور اليوم.</p></article>
                </div>
                <div class="rule-band reveal">
                    <div><strong>حالات الحجز</strong><span>Pending · Completed · Cancelled</span></div>
                    <div><strong>قاعدة زمنية</strong><span>فاصل 10 دقائق بين المواعيد</span></div>
                    <div><strong>نظام الدور</strong><span>إعادة ترتيب أرقام حجوزات اليوم</span></div>
                </div>
                <p class="scope-note">تنبيه مهم: مسار إنشاء الحجز الحالي مضبوط على مواعيد اليوم فقط. لا يتم تسويق حجز الأيام المستقبلية كميزة متاحة في النسخة الحالية.</p>
            </div>
        </section>

        <!-- CUSTOMER EXPERIENCE -->
        <section class="section customer-section soft-section" id="customer">
            <div class="container">
                <div class="section-heading reveal">
                    <span class="eyebrow"><span></span> الواجهة العامة وحساب العميل</span>
                    <h2>من أول زيارة للموقع،<br><em>لحد متابعة حجزه.</em></h2>
                    <p>العميل يقدر يتعرف على الصالون والخدمات والأسعار، ينشئ حسابًا، ويشاهد حجوزاته الحالية والسابقة.</p>
                </div>
                <div class="customer-grid">
                    <article class="customer-card customer-card-feature reveal">
                        <div class="card-label"><span>01</span><small>PUBLIC LANDING PAGE</small></div>
                        <div class="media-frame customer-image customer-image-large"><img class="asset-img" src="{{ asset('demo/images/website-home.png') }}" alt="الصفحة الرئيسية العامة للمحل" loading="lazy"><div class="asset-placeholder"><small>PUBLIC WEBSITE</small><strong>صورة الصفحة الرئيسية</strong><code>website-home.webp</code></div></div>
                        <div class="customer-card-copy"><h3>واجهة تعرّف الناس بالمحل.</h3><p>Hero carousel، نبذة عن المكان، الخدمات، الأسعار، آراء معروضة، ودعوة لطلب موعد.</p></div>
                    </article>
                    <article class="customer-card reveal delay-1">
                        <div class="card-label"><span>02</span><small>SERVICES & PRICING</small></div>
                        <div class="media-frame customer-image"><img class="asset-img" src="{{ asset('demo/images/website-services-pricing.png') }}" alt="صفحة الخدمات والأسعار للعميل" loading="lazy"><div class="asset-placeholder"><small>SERVICES & PRICING</small><strong>صورة الخدمات والأسعار</strong><code>website-services-pricing.webp</code></div></div>
                        <div class="customer-card-copy"><h3>الخدمات وأسعارها.</h3><p>عرض مجموعات الخدمات والخدمات الفرعية وأسعارها ومددها.</p></div>
                    </article>
                    <article class="customer-card reveal delay-2">
                        <div class="card-label"><span>03</span><small>BOOKING EXPERIENCE</small></div>
                        <div class="media-frame customer-image"><img class="asset-img" src="{{ asset('demo/images/website-booking.png') }}" alt="نموذج طلب الحجز" loading="lazy"><div class="asset-placeholder"><small>BOOKING FORM</small><strong>صورة تجربة إنشاء الحجز</strong><code>website-booking.webp</code></div></div>
                        <div class="customer-card-copy"><h3>طلب الموعد.</h3><p>بدء الحجز واختيار الخدمات والموظف والوقت المتاح حسب قواعد النظام.</p></div>
                    </article>
                    <article class="customer-card reveal delay-1">
                        <div class="card-label"><span>04</span><small>CUSTOMER ACCOUNT</small></div>
                        <div class="media-frame customer-image"><img class="asset-img" src="{{ asset('demo/images/profile.png') }}" alt="حجوزات العميل داخل حسابه" loading="lazy"><div class="asset-placeholder"><small>MY BOOKINGS</small><strong>صورة حجوزات العميل</strong><code>customer-bookings.webp</code></div></div>
                        <div class="customer-card-copy"><h3>حساب العميل وحجوزاته.</h3><p>الملف الشخصي، الحجوزات الحالية، وسجل الحجوزات السابقة.</p></div>
                    </article>
                </div>
            </div>
        </section>

        <!-- MANAGEMENT MODULES -->
        <section class="section modules-section dark-section" id="modules">
            <div class="container">
                <div class="section-heading section-heading-light reveal">
                    <span class="eyebrow eyebrow-light"><span></span> أدوات المدير لإدارة التشغيل</span>
                    <h2>كل قسم له وظيفة،<br><em>وكل وظيفة بتحل مشكلة يومية.</em></h2>
                    <p>إدارة البيانات والعمليات الأساسية للمحل من لوحة الإدارة، مع صلاحيات تختلف حسب دور المستخدم.</p>
                </div>
                <div class="modules-grid">
                    <article class="module-card reveal"><label>01 / BOOKINGS</label><span class="module-icon">▦</span><h3>الحجوزات</h3><p>إنشاء ومراجعة وتعديل الحجوزات، البحث والتصفية، ومتابعة الحالات وإجراءات الإدارة.</p><small>إدارة مواعيد العملاء</small></article>
                    <article class="module-card reveal delay-1"><label>02 / EMPLOYEES</label><span class="module-icon">♙</span><h3>الموظفون والحلاقون</h3><p>إضافة الموظفين وتعديل بياناتهم وتفعيلهم أو إيقافهم وإسناد الأدوار المناسبة.</p><small>إدارة فريق العمل</small></article>
                    <article class="module-card reveal delay-2"><label>03 / SERVICES</label><span class="module-icon">✂</span><h3>الخدمات والأسعار</h3><p>تقسيم الخدمات إلى مجموعات وخدمات فرعية، مع تحديد السعر والمدة لكل خدمة.</p><small>تنظيم قائمة الخدمات</small></article>
                    <article class="module-card reveal delay-3"><label>04 / CUSTOMERS</label><span class="module-icon">♧</span><h3>العملاء</h3><p>عرض وإدارة حسابات العملاء والبحث بالاسم أو البريد الإلكتروني.</p><small>الرجوع لبيانات العملاء</small></article>
                    <article class="module-card reveal"><label>05 / INVENTORY</label><span class="module-icon">▤</span><h3>المخزون والموارد</h3><p>إدارة الأصناف والـ SKU والوحدات والكميات والحد الأدنى، مع تتبع الاستخدام والتنبيه عند انخفاض المخزون.</p><small>متابعة موارد المحل</small></article>
                    <article class="module-card reveal delay-1"><label>06 / EXPENSES</label><span class="module-icon">◫</span><h3>المصروفات</h3><p>تسجيل المصروفات وتصنيفها وإضافة المورد والملاحظات ومرجع الإيصال وصوره.</p><small>تنظيم المصروفات والإيصالات</small></article>
                </div>
            </div>
        </section>

        <!-- ROLE-BASED ACCESS -->
        <section class="section roles-section">
            <div class="container">
                <div class="section-heading reveal">
                    <span class="eyebrow"><span></span> صلاحيات مبنية على دور المستخدم</span>
                    <h2>أربع تجارب مختلفة،<br><em>كل شخص يشوف اللي يخصه.</em></h2>
                    <p>النظام يفصل بين حساب العميل وحساب فريق العمل، ويمنح المدير صلاحيات الإدارة الكاملة.</p>
                </div>
                <div class="roles-grid">
                    <article class="role-card reveal"><span class="role-number">01</span><label>CUSTOMER</label><h3>العميل</h3><p>إنشاء حساب والتحقق من البريد، واستعراض الملف الشخصي والحجوزات الحالية والسابقة.</p><small>حسابه وحجوزاته الشخصية</small></article>
                    <article class="role-card reveal delay-1"><span class="role-number">02</span><label>BARBER</label><h3>الحلاق</h3><p>متابعة الحجوزات المسندة إليه والطابور اليومي، وإكمال الحجز أو إلغاؤه وفق الصلاحيات.</p><small>متابعة شغله اليومي</small></article>
                    <article class="role-card reveal delay-2"><span class="role-number">03</span><label>EMPLOYEE</label><h3>الموظف</h3><p>الوصول إلى لوحة الموظف والحجوزات والموارد والملف الشخصي حسب الدور والصلاحيات.</p><small>مهام الموظف المصرّح بها</small></article>
                    <article class="role-card reveal delay-3"><span class="role-number">04</span><label>ADMIN</label><h3>المدير</h3><p>متابعة لوحة الإدارة وإدارة الموظفين والعملاء والحجوزات والخدمات والمخزون والمصروفات.</p><small>إشراف على تشغيل المحل</small></article>
                </div>
            </div>
        </section>

        <!-- AUTH & NOTIFICATIONS -->
        <section class="section auth-section soft-section">
            <div class="container auth-layout">
                <div class="section-copy reveal">
                    <span class="eyebrow"><span></span> الحسابات والتنبيهات</span>
                    <h2>حسابات منفصلة،<br><em>وتنبيهات للعمليات المهمة.</em></h2>
                    <p>تسجيل العميل يمر بخطوة تحقق من البريد الإلكتروني، بينما حسابات الإدارة والموظفين لها مسارات دخول وصلاحيات منفصلة.</p>
                    <ul class="check-list compact-list">
                        <li><b>✓</b><span><strong>تسجيل العميل عبر OTP</strong><small>رمز تحقق من 6 أرقام يُرسل بالبريد قبل إنشاء الحساب.</small></span></li>
                        <li><b>✓</b><span><strong>استعادة كلمة المرور</strong><small>تدفقات إعادة تعيين كلمة المرور للعملاء والموظفين.</small></span></li>
                        <li><b>✓</b><span><strong>إشعارات الحجز</strong><small>إشعارات بريد وقاعدة بيانات للعميل والموظف عند إنشاء الحجز.</small></span></li>
                    </ul>
                </div>
                <div class="auth-visual reveal delay-1">
                    <div class="media-frame auth-shot"><img class="asset-img" src="{{ asset('demo/images/auth-email-otp.png') }}" alt="واجهة التحقق من البريد الإلكتروني برمز OTP" loading="lazy"><div class="asset-placeholder"><small>EMAIL OTP VERIFICATION</small><strong>صورة شاشة التحقق من البريد</strong><code>auth-email-otp.webp</code></div></div>
                    <div class="auth-note"><span>✉</span><div><b>6-digit email verification</b><small>Queued email notifications</small></div></div>
                </div>
            </div>
        </section>

        <!-- GALLERY -->
        <section class="section gallery-section" id="gallery">
            <div class="container">
                <div class="gallery-heading reveal">
                    <div><span class="eyebrow"><span></span> جولة بالصور</span><h2>تفاصيل الواجهات،<br><em>من غير كلام كتير.</em></h2></div>
                    <p>استبدل الأماكن دي بلقطات شاشة من النسخة الحقيقية. اضغط على الصورة لعرضها بالحجم الكامل.</p>
                </div>
                <div class="gallery-controls reveal" role="group" aria-label="فلترة صور المشروع">
                    <button class="gallery-filter active" type="button" data-filter="all" aria-pressed="true">الكل</button>
                    <button class="gallery-filter" type="button" data-filter="dashboard" aria-pressed="false">Dashboard</button>
                    <button class="gallery-filter" type="button" data-filter="management" aria-pressed="false">الإدارة</button>
                    <button class="gallery-filter" type="button" data-filter="website" aria-pressed="false">الموقع والعميل</button>
                </div>
                <div class="gallery-grid" id="galleryGrid">
                    <button class="gallery-item gallery-wide reveal" type="button" data-category="dashboard" data-full="{{ asset('demo/images/hero-dashboard.png') }}" data-caption="ملخص لوحة تحكم المدير"><span class="gallery-media media-frame"><img class="asset-img" src="{{ asset('demo/images/hero-dashboard.png') }}" alt="ملخص لوحة التحكم" loading="lazy"><span class="asset-placeholder"><strong>Dashboard Overview</strong><code>dashboard-overview.webp</code></span></span><span class="gallery-caption"><b>ملخص لوحة التحكم</b><small>DASHBOARD / 01</small><i>↗</i></span></button>
                    <button class="gallery-item reveal delay-1" type="button" data-category="dashboard" data-full="{{ asset('demo/images/booking-dashboard.png') }}" data-caption="الحجوزات الأخيرة"><span class="gallery-media media-frame"><img class="asset-img" src="{{ asset('demo/images/booking-dashboard.png') }}" alt="الحجوزات الأخيرة في لوحة التحكم" loading="lazy"><span class="asset-placeholder"><strong>Recent Bookings</strong><code>dashboard-recent-bookings.webp</code></span></span><span class="gallery-caption"><b>الحجوزات الأخيرة</b><small>DASHBOARD / 02</small><i>↗</i></span></button>
                    <button class="gallery-item reveal" type="button" data-category="dashboard" data-full="{{ asset('demo/images/resource.png') }}" data-caption="الأصناف منخفضة المخزون"><span class="gallery-media media-frame"><img class="asset-img" src="{{ asset('demo/images/resource.png') }}" alt="الأصناف منخفضة المخزون" loading="lazy"><span class="asset-placeholder"><strong>Low Stock Items</strong><code>dashboard-low-stock.webp</code></span></span><span class="gallery-caption"><b>الأصناف منخفضة المخزون</b><small>DASHBOARD / 03</small><i>↗</i></span></button>
                    <button class="gallery-item reveal delay-1" type="button" data-category="management" data-full="{{ asset('demo/images/booking.png') }}" data-caption="إدارة الحجوزات"><span class="gallery-media media-frame"><img class="asset-img" src="{{ asset('demo/images/booking.png') }}" alt="صفحة إدارة الحجوزات" loading="lazy"><span class="asset-placeholder"><strong>Bookings Management</strong><code>management-bookings.webp</code></span></span><span class="gallery-caption"><b>إدارة الحجوزات</b><small>MANAGEMENT / 01</small><i>↗</i></span></button>
                    <button class="gallery-item reveal" type="button" data-category="management" data-full="{{ asset('demo/images/employee-dashboard.png') }}" data-caption="إدارة الموظفين"><span class="gallery-media media-frame"><img class="asset-img" src="{{ asset('demo/images/employee-dashboard.png') }}" alt="صفحة إدارة الموظفين" loading="lazy"><span class="asset-placeholder"><strong>Employees</strong><code>management-employees.webp</code></span></span><span class="gallery-caption"><b>إدارة الموظفين</b><small>MANAGEMENT / 02</small><i>↗</i></span></button>
                    <button class="gallery-item reveal delay-1" type="button" data-category="management" data-full="{{ asset('demo/images/user-dashboard.png') }}" data-caption="إدارة العملاء"><span class="gallery-media media-frame"><img class="asset-img" src="{{ asset('demo/images/user-dashboard.png') }}" alt="صفحة إدارة العملاء" loading="lazy"><span class="asset-placeholder"><strong>Customers</strong><code>management-customers.webp</code></span></span><span class="gallery-caption"><b>إدارة العملاء</b><small>MANAGEMENT / 03</small><i>↗</i></span></button>
                    <button class="gallery-item reveal" type="button" data-category="management" data-full="{{ asset('demo/images/services-dashboard.png') }}" data-caption="إدارة الخدمات والأسعار"><span class="gallery-media media-frame"><img class="asset-img" src="{{ asset('demo/images/services-dashboard.png') }}" alt="إدارة الخدمات والأسعار" loading="lazy"><span class="asset-placeholder"><strong>Services & Pricing</strong><code>management-services.webp</code></span></span><span class="gallery-caption"><b>الخدمات والأسعار</b><small>MANAGEMENT / 04</small><i>↗</i></span></button>
                    <button class="gallery-item reveal delay-1" type="button" data-category="management" data-full="{{ asset('demo/images/mange-inventory.png') }}" data-caption="إدارة المخزون والموارد"><span class="gallery-media media-frame"><img class="asset-img" src="{{ asset('demo/images/mange-inventory.png') }}" alt="إدارة المخزون والموارد" loading="lazy"><span class="asset-placeholder"><strong>Inventory</strong><code>management-inventory.webp</code></span></span><span class="gallery-caption"><b>المخزون والموارد</b><small>MANAGEMENT / 05</small><i>↗</i></span></button>
                    <button class="gallery-item reveal" type="button" data-category="management" data-full="{{ asset('demo/images/management-expenses.png') }}" data-caption="إدارة المصروفات والإيصالات"><span class="gallery-media media-frame"><img class="asset-img" src="{{ asset('demo/images/management-expenses.png') }}" alt="إدارة المصروفات والإيصالات" loading="lazy"><span class="asset-placeholder"><strong>Expenses</strong><code>management-expenses.webp</code></span></span><span class="gallery-caption"><b>المصروفات والإيصالات</b><small>MANAGEMENT / 06</small><i>↗</i></span></button>
                    <button class="gallery-item gallery-wide reveal delay-1" type="button" data-category="website" data-full="{{ asset('demo/images/website-home.png') }}" data-caption="الواجهة العامة للمحل"><span class="gallery-media media-frame"><img class="asset-img" src="{{ asset('demo/images/website-home.png') }}" alt="الواجهة العامة للمحل" loading="lazy"><span class="asset-placeholder"><strong>Public Website</strong><code>website-home.webp</code></span></span><span class="gallery-caption"><b>الواجهة العامة للمحل</b><small>WEBSITE / 01</small><i>↗</i></span></button>
                    <button class="gallery-item reveal" type="button" data-category="website" data-full="{{ asset('demo/images/website-services-pricing.png') }}" data-caption="قائمة الخدمات والأسعار"><span class="gallery-media media-frame"><img class="asset-img" src="{{ asset('demo/images/website-services-pricing.png') }}" alt="قائمة الخدمات والأسعار" loading="lazy"><span class="asset-placeholder"><strong>Services & Prices</strong><code>website-services-pricing.webp</code></span></span><span class="gallery-caption"><b>قائمة الخدمات والأسعار</b><small>WEBSITE / 02</small><i>↗</i></span></button>
                    <button class="gallery-item reveal delay-1" type="button" data-category="website" data-full="{{ asset('demo/images/website-booking.png') }}" data-caption="طلب حجز موعد"><span class="gallery-media media-frame"><img class="asset-img" src="{{ asset('demo/images/website-booking.png') }}" alt="طلب حجز موعد" loading="lazy"><span class="asset-placeholder"><strong>Booking Form</strong><code>website-booking.webp</code></span></span><span class="gallery-caption"><b>طلب حجز موعد</b><small>WEBSITE / 03</small><i>↗</i></span></button>
                    <button class="gallery-item reveal" type="button" data-category="website" data-full="{{ asset('demo/images/profile.png') }}" data-caption="حجوزات العميل"><span class="gallery-media media-frame"><img class="asset-img" src="{{ asset('demo/images/profile.png') }}" alt="حجوزات العميل" loading="lazy"><span class="asset-placeholder"><strong>Customer Bookings</strong><code>customer-bookings.webp</code></span></span><span class="gallery-caption"><b>حجوزات العميل</b><small>WEBSITE / 04</small><i>↗</i></span></button>
                    <button class="gallery-item reveal delay-1" type="button" data-category="website" data-full="{{ asset('demo/images/auth-email-otp.png') }}" data-caption="التحقق من البريد الإلكتروني"><span class="gallery-media media-frame"><img class="asset-img" src="{{ asset('demo/images/auth-email-otp.png') }}" alt="التحقق من البريد الإلكتروني" loading="lazy"><span class="asset-placeholder"><strong>Email OTP</strong><code>auth-email-otp.webp</code></span></span><span class="gallery-caption"><b>التحقق من البريد</b><small>WEBSITE / 05</small><i>↗</i></span></button>
                </div>
            </div>

        </section>

        <!-- BENEFITS -->
        <section class="section value-section soft-section" id="why-buy">
            <div class="container value-layout">
                <div class="section-copy value-intro reveal">
                    <span class="eyebrow"><span></span> القيمة العملية لصاحب المحل</span>
                    <h2>ابدأ من نظام موجود،<br><em>وطوّره حسب احتياجك.</em></h2>
                    <p>بدل بناء واجهة العميل ولوحة الإدارة وتدفقات الحجز من الصفر، المشروع بيقدم أساسًا يجمع العمليات الأساسية في تطبيق واحد.</p>
                    <a class="btn btn-dark" href="#contact">اسأل عن تفاصيل النسخة <span>↗</span></a>
                </div>
                <div class="value-list reveal delay-1">
                    <article><b>01</b><span><strong>عمليات مترابطة</strong><small>الخدمات والموظفون والحجوزات ونظام الدور ضمن دورة تشغيل واحدة.</small></span><i>↗</i></article>
                    <article><b>02</b><span><strong>متابعة فريق العمل</strong><small>أدوار وصلاحيات منفصلة، مع صفحات مخصصة للمدير والموظفين.</small></span><i>↗</i></article>
                    <article><b>03</b><span><strong>رؤية أفضل للموارد</strong><small>متابعة الأصناف والاستهلاك والمصروفات والإيصالات.</small></span><i>↗</i></article>
                    <article><b>04</b><span><strong>أساس تقني حديث</strong><small>Laravel 13 وLivewire 4 وTailwind CSS وSpatie Permission.</small></span><i>↗</i></article>
                </div>
            </div>
        </section>

        <!-- TECH STACK -->
        <section class="tech-section">
            <div class="container tech-inner"><span>BUILT WITH</span><b>Laravel 13</b><i></i><b>Livewire 4</b><i></i><b>Tailwind CSS</b><i></i><b>Spatie Permission</b><i></i><b>Vite</b></div>
        </section>

        <!-- FAQ / HONEST SCOPE -->
        <section class="section faq-section" id="faq">
            <div class="container faq-layout">
                <div class="section-copy reveal"><span class="eyebrow"><span></span> قبل ما تقرر</span><h2>إمكانيات واضحة،<br><em>من غير وعود زيادة.</em></h2><p>دي حدود النسخة الحالية طبقًا لفحص الكود. أي وظيفة غير موجودة موضحة بصراحة بدل ما تتحول لمفاجأة بعد الشراء.</p></div>
                <div class="faq-list reveal delay-1">
                    <details><summary>هل النظام فيه دفع إلكتروني للحجوزات؟</summary><p>لا. لا توجد بوابة دفع أو حالة دفع مرتبطة بالحجز. الموجود هو تسجيل المصروفات وطرق دفع المصروفات، وده مختلف عن تحصيل قيمة الحجز من العميل.</p></details>
                    <details><summary>هل أقدر أحجز ليوم مستقبلي؟</summary><p>مسار إنشاء الحجز الحالي مضبوط على مواعيد اليوم فقط. دعم الأيام المستقبلية يحتاج تعديلًا قبل اعتباره ميزة متاحة.</p></details>
                    <details><summary>هل أقدر أدير أكثر من فرع؟</summary><p>النسخة الحالية موجهة لتشغيل موقع واحد، ولم يتم العثور على نظام لإدارة فروع متعددة.</p></details>
                    <details><summary>هل توجد إشعارات WhatsApp أو SMS؟</summary><p>الإشعارات الموجودة هي البريد الإلكتروني وإشعارات قاعدة البيانات. لا يوجد تكامل WhatsApp أو SMS في النسخة التي تم فحصها.</p></details>
                    <details><summary>هل لوحة التحكم تتحدث لحظيًا؟</summary><p>لم يتم العثور على تكامل WebSockets أو تحديثات لحظية في النسخة الحالية.</p></details>
                    <details><summary>هل توجد إدارة لتقييمات العملاء؟</summary><p>الصفحة العامة تحتوي على قسم آراء معروضة، لكن لا يوجد نظام متكامل لإدارة التقييمات والمراجعات.</p></details>
                    <details><summary>هل أستطيع تعديل الحجز أو إلغاءه؟</summary><p>توجد صفحات تعديل وإجراءات إلغاء للحجوزات حسب الصلاحية والحالة. الإلغاء متاح للحجوزات المعلّقة، ولا توجد عملية مستقلة لإعادة الجدولة كميزة منفصلة.</p></details>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="contact-section dark-section" id="contact">
            <div class="contact-halo"></div>
            <div class="container contact-content reveal">
                <span class="eyebrow eyebrow-light"><span></span> الخطوة التالية</span>
                <h2>شايف طريقة الشغل؟<br><em>خلينا نتكلم عن احتياجك.</em></h2>
                <p>تواصل لمعرفة تفاصيل الحصول على نسخة من المشروع، ومراجعة ما إذا كانت إمكانياته الحالية مناسبة لطريقة تشغيل محلك.</p>
                <a class="btn btn-gold btn-large" href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">تواصل للاستفسار عن الشراء <span>↗</span></a>
                <small>{{ $productName }} · BARBER MANAGEMENT SYSTEM</small>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container footer-inner">
            <a href="#top" class="brand"><span class="brand-symbol">B<span>.</span></span><span class="brand-text"><strong>{{ $productName }}</strong><small>BARBER MANAGEMENT SYSTEM</small></span></a>
            <p>إدارة الحجوزات وتشغيل صالون الحلاقة في مكان واحد.</p>
            <a href="#top" class="to-top">العودة للأعلى ↑</a>
        </div>
    </footer>

    <!-- Gallery lightbox -->
    <div class="lightbox" id="lightbox" aria-hidden="true" role="dialog" aria-modal="true" aria-label="عرض صورة المشروع">
        <button class="lightbox-close" id="lightboxClose" type="button" aria-label="إغلاق">×</button>
        <button class="lightbox-arrow prev" id="lightboxPrev" type="button" aria-label="الصورة السابقة">‹</button>
        <figure><img id="lightboxImage" src="" alt=""><figcaption id="lightboxCaption"></figcaption></figure>
        <button class="lightbox-arrow next" id="lightboxNext" type="button" aria-label="الصورة التالية">›</button>
    </div>

    <script src="{{ asset('demo/js/demo.js') }}" defer></script>
</body>
</html>
