<footer id="contact" class="w-full bg-[#f8f8f8] py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid gap-10 text-right sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <h3 class="heading-font text-3xl text-[#5d3026]">باربر هاوس</h3>
                <p class="mt-4 leading-8 text-gray-600">خدمات الحلاقة والحجز الإلكتروني في مكان واحد.</p>
            </div>
            <div>
                <h4 class="mb-4 font-bold text-[#5d3026]">تصفّح الموقع</h4>
                <nav class="flex flex-col gap-3 text-gray-700">
                    <a href="#services" class="hover:text-[#c98a6a]">الخدمات</a>
                    <a href="#prices" class="hover:text-[#c98a6a]">الأسعار</a>
                    <a href="#booking" class="hover:text-[#c98a6a]">الحجز</a>
                </nav>
            </div>
            <div>
                <h4 class="mb-4 font-bold text-[#5d3026]">الحجوزات</h4>
                <p class="leading-7 text-gray-600">استخدم نموذج الحجز الموجود في الصفحة لاختيار الخدمة والموعد.</p>
                <a href="#booking" class="mt-4 inline-flex rounded-lg bg-[#5d3026] px-5 py-2.5 font-semibold text-white transition hover:bg-[#7b493a]">احجز موعدك</a>
            </div>
            <div>
                <h4 class="mb-4 font-bold text-[#5d3026]">بيانات محدّثة</h4>
                <p class="leading-7 text-gray-600">تظهر الخدمات والأسعار المسجّلة في لوحة التحكم، لذلك لن نعرض أرقام تواصل أو حسابات اجتماعية غير مفعّلة.</p>
            </div>
        </div>
        <div class="mt-12 border-t border-gray-200 pt-6 text-center text-xs text-gray-500">© {{ now()->year }} باربر هاوس. جميع الحقوق محفوظة.</div>
    </div>
</footer>
@livewireScripts
@stack('scripts')
</body>
</html>
