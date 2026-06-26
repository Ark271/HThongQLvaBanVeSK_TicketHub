<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminEventController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\OrganizerEventController;

/* HOME */
Route::get('/', [HomeController::class, 'index'])->name('home');


/* AUTH (LOGIN / LOGOUT / REGISTER) */
Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->name('login');

Route::post('/login', [LoginController::class, 'login']);

Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

/* USER ROUTES */

// Danh sách sự kiện (mua vé)
Route::get('/events', [EventController::class, 'index'])
    ->name('events.index');

// Chi tiết sự kiện
Route::get('/events/{id}', [EventController::class, 'show'])
    ->name('events.show');

// Đăng ký tham gia / mua vé
Route::post('/participants', [ParticipantController::class, 'store'])
    ->middleware('auth')
    ->name('participants.store');

// Trang cá nhân (TÀI KHOẢN)
Route::middleware('auth')->group(function () {

    // Trang tài khoản (info / events / password / delete)
    Route::get('/profile', [ProfileController::class, 'index'])
        ->name('profile');

    // Chỉnh sửa thông tin cá nhân
    Route::put('/profile/information', [ProfileController::class, 'updateInformation'])
        ->name('profile.information.update');

    // Đổi mật khẩu (POST)
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password');

    // Xóa tài khoản
    Route::post('/profile/delete', [ProfileController::class, 'destroy'])
        ->name('profile.delete');

    Route::post('/profile/orders/{order}/cancel', [ProfileController::class, 'cancelOrder'])
        ->name('profile.orders.cancel');
});


/* ADMIN ROUTES */

Route::prefix('admin')->middleware(['auth', 'is_admin'])
    ->name('admin.')
    ->group(function () {

    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::resource('events', AdminEventController::class);

    Route::get('/users', [AdminUserController::class, 'index'])
        ->name('users.index');

    Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])
        ->name('users.updateRole');

    Route::patch('/users/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])
        ->name('users.toggleActive');

    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])
        ->name('users.destroy');

    Route::get('/dashboard/export-excel', [AdminController::class, 'exportExcel'])
    ->name('dashboard.exportExcel');
});


/* ORGANIZER ROUTES */
Route::prefix('organizer')
    ->middleware(['auth', 'is_organizer'])
    ->name('organizer.')
    ->group(function () {

        Route::get('/', [OrganizerController::class, 'dashboard'])
            ->name('dashboard');

        Route::resource('events', OrganizerEventController::class);

        Route::get('/dashboard/export-excel',[OrganizerController::class, 'exportExcel'])
        ->name('dashboard.exportExcel');
    });

/* PAYMENT ROUTES */

Route::post('/vnpay_payment', [PaymentController::class, 'vnpay_payment'])->name('vnpay.payment');
Route::get('/vnpay_return', [PaymentController::class, 'vnpay_return'])->name('vnpay.return');


Auth::routes();
