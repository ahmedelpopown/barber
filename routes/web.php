<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AuthAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\CustomerGoogleAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PasswordChangeRequestController;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
    Route::view('/project-demo', 'demo')
        ->name('project.demo');

Route::get(
    '/auth/google/redirect',
    [CustomerGoogleAuthController::class, 'redirect']
)->name('customer.google.redirect');

Route::get(
    '/auth/google/callback',
    [CustomerGoogleAuthController::class, 'callback']
)->name('customer.google.callback');

 Route::get('/', HomeController::class)->name('home');

/*
|--------------------------------------------------------------------------
| Password Recovery (Customers, Employees and Admins)
|--------------------------------------------------------------------------
*/
Route::get('/forgot-password', [PasswordController::class, 'showForgotForm'])
    ->name('password.request');

Route::post('/forgot-password', [PasswordController::class, 'sendResetLink'])
    ->middleware('throttle:5,1')
    ->name('password.email');

Route::get('/reset-password/{token}', [PasswordController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('/reset-password', [PasswordController::class, 'resetPassword'])
    ->middleware('throttle:5,1')
    ->name('password.update');

/*
|--------------------------------------------------------------------------
| Password Change Approval
|--------------------------------------------------------------------------
*/
Route::get('/password-change/confirm/{token}', [PasswordChangeRequestController::class, 'show'])
    ->name('password-change.confirm');

Route::post('/password-change/confirm/{token}', [PasswordChangeRequestController::class, 'decide'])
    ->middleware('throttle:10,1')
    ->name('password-change.decide');

Route::get('/password-change/response', [PasswordChangeRequestController::class, 'response'])
    ->name('password-change.response');

Route::get('/password-change/invalid', [PasswordChangeRequestController::class, 'invalid'])
    ->name('password-change.invalid');


/*
|--------------------------------------------------------------------------
| Customer Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest:customer')->group(function () {

    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:3,1')
        ->name('register.submit');


    /*
    |--------------------------------------------------------------------------
    | Email Verification
    |--------------------------------------------------------------------------
    */

    Route::get('/verify-email', [AuthController::class, 'showVerificationForm'])
        ->name('verification.notice');

    Route::post('/verify-email', [AuthController::class, 'verifyOtp'])
        ->middleware('throttle:6,1')
        ->name('verification.verify');

    Route::post('/verify-email/resend', [AuthController::class, 'resendOtp'])
        ->middleware('throttle:1,1')
        ->name('verification.resend');
});


Route::post('/logout-user', [AuthController::class, 'logout'])
    ->middleware('auth:customer')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:customer')->group(function () {

    Route::livewire(
        '/profile',
        'pages::customer.profile.show'
    )->name('customer.profile');

    Route::livewire(
        '/profile/bookings',
        'pages::customer.profile.bookings'
    )->name('customer.profile.bookings');

    Route::livewire(
        '/profile/current-booking',
        'pages::customer.profile.current-booking'
    )->name('customer.profile.current-booking');
});

/*
|--------------------------------------------------------------------------
| Employee / Admin Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest:employee')->group(function () {

    Route::get('/employee/login', function () {
        return view('admin.login');
    })->name('employee.login');

    Route::post('/employee/login', [AuthAdminController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('employee.login.submit');
});


Route::post('/employee/logout-emp', [AuthAdminController::class, 'logout'])
    ->middleware('auth:employee')
    ->name('employee.logout');


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware([
        'auth:employee',
        'role:admin',
    ])
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', DashboardController::class)
            ->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Employees
        |--------------------------------------------------------------------------
        */

        Route::livewire('/employees', 'pages::employees.index')
            ->name('employees.index');

        Route::livewire('/employees/create', 'pages::employees.create')
            ->name('employees.create');

        Route::livewire('/employees/{employee}/edit', 'pages::employees.edit')
            ->name('employees.edit');

        Route::livewire('/employees/{employee}/show', 'pages::employees.show')
            ->name('employees.show');


        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */

        Route::livewire('/users', 'pages::users.index')
            ->name('users.index');

        Route::livewire('/users/create', 'pages::users.create')
            ->name('users.create');

        Route::livewire('/users/{user}/edit', 'pages::users.edit')
            ->name('users.edit');


        /*
        |--------------------------------------------------------------------------
        | Bookings
        |--------------------------------------------------------------------------
        */

        Route::livewire('/bookings', 'pages::bookings.index')
            ->name('bookings.index');

        Route::livewire('/bookings/create', 'pages::bookings.create')
            ->name('bookings.create');

        Route::livewire('/bookings/{booking}/edit', 'pages::bookings.edit')
            ->name('bookings.edit');


        /*
        |--------------------------------------------------------------------------
        | Services
        |--------------------------------------------------------------------------
        */

        Route::livewire('/services', 'pages::services.index')
            ->name('services.index');

        Route::livewire('/services/create', 'pages::services.create')
            ->name('services.create');

        Route::livewire('/services/{service}/edit', 'pages::services.edit')
            ->name('services.edit');


        /*
        |--------------------------------------------------------------------------
        | Inventory / Resources
        |--------------------------------------------------------------------------
        */
        Route::livewire('/resources', 'pages::resources.index')
            ->name('resources.index');

        Route::livewire('/resources/create', 'pages::resources.create')
            ->name('resources.create');

        Route::livewire('/resources/{resource}/edit', 'pages::resources.edit')
            ->name('resources.edit');

        Route::livewire('/expenses', 'pages::expenses.index')
            ->name('expenses.index');

        Route::livewire('/expenses/create', 'pages::expenses.create')
            ->name('expenses.create');

        Route::livewire('/expenses/{expense}', 'pages::expenses.show')
            ->name('expenses.show');

        Route::livewire('/expenses/{expense}/edit', 'pages::expenses.edit')
            ->name('expenses.edit');
    });


/*
|--------------------------------------------------------------------------
| Employee / Barber Routes
|--------------------------------------------------------------------------
*/

Route::prefix('employee')
    ->middleware(['auth:employee', 'role:employee|barber'])
    ->name('employee.')
    ->group(function () {

        Route::livewire('/dashboard', 'pages::employees.dashboard')
            ->name('dashboard');


        Route::livewire('/resources', 'pages::resources.index')
            ->name('resources.index');

        Route::livewire('/bookings', 'pages::bookings.index')
            ->name('bookings.index');

        Route::livewire('/bookings/create', 'pages::bookings.create')
            ->name('bookings.create');

        Route::livewire('/bookings/{booking}/edit', 'pages::bookings.edit')
            ->name('bookings.edit');

        Route::livewire('/profile', 'pages::employees.profile')
            ->name('profile.show');
    });
