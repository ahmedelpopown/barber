# باربر هاوس - نسخة بعد المراجعة

## تشغيل المشروع

1. ثبّت PHP/Composer وNode.js بالإصدارات المناسبة لـ `composer.json` و`package.json`.
2. من مجلد المشروع نفّذ:

```powershell
composer install
npm ci
npm run build
php artisan optimize:clear
php artisan storage:link
```

3. إذا لم يكن `.env` موجودًا، انسخ `.env.example` إلى `.env` وشغّل `php artisan key:generate`.
4. راجع إعداد قاعدة البيانات وخذ نسخة احتياطية قبل أي migrations.
5. اختبر الصفحات:

```powershell
php artisan test --filter=PageSmokeTest
php artisan test
```

## أهم التعديلات

- إصلاح الـ Loader وإضافة مهلة أمان CSS/JavaScript.
- توصيل ملفات Livewire في الـ layouts.
- إضافة Dashboard إدارية مرتبطة ببيانات النظام الحقيقية.
- جعل الخدمات والأسعار في الموقع مأخوذة من قاعدة البيانات بدل القيم التجريبية.
- معالجة الصور الغائبة، وتنظيف بعض المحتوى غير الحقيقي، وإصلاح أسماء صفحات العملاء التي يستخدمها Livewire 4.

التفاصيل والقيود موجودة في `AUDIT_REPORT_AR.md`. اختبارات Laravel لم تُشغّل في بيئة إعداد الحزمة بسبب غياب `vendor/` وComposer؛ يجب تشغيلها بعد التثبيت.
