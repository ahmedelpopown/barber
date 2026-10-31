<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="باربر هاوس هو صالون رجالي يقدّم حلاقة مميزة، خدمات عناية، وبيئة راقية للرجال في قلب القاهرة.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Aref+Ruqaa+Ink:wght@400;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <title>باربر هاوس | حلاقة رجالية راقية</title>
    @stack('styles')
</head>
@php
    $isAuthLayout = request()->routeIs(
        'login',
        'register',
        'verification.notice',
        'password.*',
        'password-change.*'
    );
@endphp
<body class="m-0 flex min-h-screen flex-col bg-[#fcfaf8] text-[#2d201c] {{ $isAuthLayout ? 'auth-layout' : '' }}">