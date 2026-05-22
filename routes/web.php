<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DuePeriodController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Member\ActivityController as MemberActivityController;
use App\Http\Controllers\Member\ActivityShiftAttendanceController;
use App\Http\Controllers\Member\AnnouncementController as MemberAnnouncementController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Member\DueController as MemberDueController;
use App\Http\Controllers\PublicShiftAttendanceController;
use App\Http\Controllers\Webhook\WhatsappAttendanceWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));
Route::post('/webhook/whatsapp/absensi', WhatsappAttendanceWebhookController::class)->name('webhook.whatsapp.absensi');
Route::get('/absen/shift/{shift:qr_token}', [PublicShiftAttendanceController::class, 'create'])->name('public.shift-attendance.create');
Route::post('/absen/shift/{shift:qr_token}', [PublicShiftAttendanceController::class, 'store'])->name('public.shift-attendance.store');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::middleware('pengurus')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
        Route::get('/laporan', ReportController::class)->name('reports.index')->middleware('permission:reports.view');

        Route::middleware('permission:organization.manage')->group(function () {
            Route::get('/organisasi', [OrganizationController::class, 'edit'])->name('organization.edit');
            Route::put('/organisasi', [OrganizationController::class, 'update'])->name('organization.update');
        });

        Route::middleware('permission:users.view')->group(function () {
            Route::get('/pengguna', [AdminUserController::class, 'index'])->name('users.index');
        });
        Route::middleware('permission:users.manage')->group(function () {
            Route::get('/pengguna/tambah', [AdminUserController::class, 'create'])->name('users.create');
            Route::post('/pengguna', [AdminUserController::class, 'store'])->name('users.store');
            Route::get('/pengguna/{user}/ubah', [AdminUserController::class, 'edit'])->name('users.edit');
            Route::put('/pengguna/{user}', [AdminUserController::class, 'update'])->name('users.update');
            Route::delete('/pengguna/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        });

        Route::middleware('permission:members.view')->group(function () {
            Route::get('/anggota', [MemberController::class, 'index'])->name('members.index');
        });
        Route::middleware('permission:members.manage')->group(function () {
            Route::get('/anggota/tambah', [MemberController::class, 'create'])->name('members.create');
            Route::post('/anggota', [MemberController::class, 'store'])->name('members.store');
            Route::get('/anggota/{member}/ubah', [MemberController::class, 'edit'])->name('members.edit');
            Route::put('/anggota/{member}', [MemberController::class, 'update'])->name('members.update');
            Route::delete('/anggota/{member}', [MemberController::class, 'destroy'])->name('members.destroy');
        });
        Route::middleware('permission:members.view')->group(function () {
            Route::get('/anggota/{member}', [MemberController::class, 'show'])->name('members.show');
        });

        Route::middleware('permission:activities.view')->group(function () {
            Route::get('/kegiatan', [ActivityController::class, 'index'])->name('activities.index');
        });
        Route::middleware('permission:activities.manage')->group(function () {
            Route::get('/kegiatan/tambah', [ActivityController::class, 'create'])->name('activities.create');
            Route::post('/kegiatan', [ActivityController::class, 'store'])->name('activities.store');
            Route::get('/kegiatan/{activity}/ubah', [ActivityController::class, 'edit'])->name('activities.edit');
            Route::put('/kegiatan/{activity}', [ActivityController::class, 'update'])->name('activities.update');
            Route::delete('/kegiatan/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');
            Route::get('/kegiatan/{activity}/absensi', [ActivityController::class, 'attendance'])->name('activities.attendance');
            Route::post('/kegiatan/{activity}/absensi', [ActivityController::class, 'updateAttendance'])->name('activities.attendance.update');
            Route::get('/kegiatan/{activity}/shift/{shift}/petugas', [ActivityController::class, 'shiftAssignments'])->name('activities.shift.assignments');
            Route::post('/kegiatan/{activity}/shift/{shift}/petugas', [ActivityController::class, 'updateShiftAssignments'])->name('activities.shift.assignments.update');
            Route::get('/kegiatan/{activity}/shift/{shift}/absensi', [ActivityController::class, 'attendanceShift'])->name('activities.attendance.shift');
            Route::post('/kegiatan/{activity}/shift/{shift}/absensi', [ActivityController::class, 'updateShiftAttendance'])->name('activities.attendance.shift.update');
        });
        Route::middleware('permission:activities.view')->group(function () {
            Route::get('/kegiatan/{activity}', [ActivityController::class, 'show'])->name('activities.show');
            Route::get('/kegiatan/{activity}/shift/{shift}/qr-gambar', [ActivityController::class, 'shiftQrImage'])->name('activities.shift.qr-image');
            Route::get('/kegiatan/{activity}/shift/{shift}/qr-download', [ActivityController::class, 'downloadShiftQrPoster'])->name('activities.shift.qr-download');
        });

        Route::middleware('permission:finance.view')->group(function () {
            Route::get('/keuangan', [TransactionController::class, 'index'])->name('transactions.index');
            Route::get('/iuran', [DuePeriodController::class, 'index'])->name('dues.index');
        });
        Route::middleware('permission:finance.manage')->group(function () {
            Route::get('/keuangan/tambah', [TransactionController::class, 'create'])->name('transactions.create');
            Route::post('/keuangan', [TransactionController::class, 'store'])->name('transactions.store');
            Route::get('/keuangan/{transaction}/ubah', [TransactionController::class, 'edit'])->name('transactions.edit');
            Route::put('/keuangan/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
            Route::delete('/keuangan/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

            Route::get('/iuran/tambah', [DuePeriodController::class, 'create'])->name('dues.create');
            Route::post('/iuran', [DuePeriodController::class, 'store'])->name('dues.store');
            Route::get('/iuran/{period}/ubah', [DuePeriodController::class, 'edit'])->name('dues.edit');
            Route::put('/iuran/{period}', [DuePeriodController::class, 'update'])->name('dues.update');
            Route::delete('/iuran/{period}', [DuePeriodController::class, 'destroy'])->name('dues.destroy');
            Route::patch('/iuran/{period}/pembayaran/{payment}', [DuePeriodController::class, 'updatePayment'])->name('dues.payments.update');
        });
        Route::middleware('permission:finance.view')->group(function () {
            Route::get('/iuran/{period}', [DuePeriodController::class, 'show'])->name('dues.show');
        });

        Route::middleware('permission:announcements.view')->group(function () {
            Route::get('/pengumuman', [AnnouncementController::class, 'index'])->name('announcements.index');
        });
        Route::middleware('permission:announcements.manage')->group(function () {
            Route::get('/pengumuman/tambah', [AnnouncementController::class, 'create'])->name('announcements.create');
            Route::post('/pengumuman', [AnnouncementController::class, 'store'])->name('announcements.store');
            Route::get('/pengumuman/{announcement}/ubah', [AnnouncementController::class, 'edit'])->name('announcements.edit');
            Route::put('/pengumuman/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
            Route::delete('/pengumuman/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
        });
        Route::middleware('permission:announcements.view')->group(function () {
            Route::get('/pengumuman/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');
        });
    });

    Route::prefix('anggota')->name('member.')->group(function () {
        Route::get('/dashboard', MemberDashboardController::class)->name('dashboard');
        Route::get('/kegiatan', [MemberActivityController::class, 'index'])->name('activities.index')->middleware('permission:activities.view');
        Route::post('/kegiatan/{activity}/absen', [MemberActivityController::class, 'storeAttendance'])->name('activities.attendance.store')->middleware('permission:activities.view');
        Route::get('/kegiatan/{activity}', [MemberActivityController::class, 'show'])->name('activities.show')->middleware('permission:activities.view');
        Route::get('/kegiatan/{activity}/shift/{shift}/absen', [ActivityShiftAttendanceController::class, 'create'])->name('activities.shift-absen')->middleware('permission:activities.view');
        Route::post('/kegiatan/{activity}/shift/{shift}/absen', [ActivityShiftAttendanceController::class, 'store'])->name('activities.shift-absen.store')->middleware('permission:activities.view');
        Route::get('/pengumuman', [MemberAnnouncementController::class, 'index'])->name('announcements.index')->middleware('permission:announcements.view');
        Route::get('/pengumuman/{announcement}', [MemberAnnouncementController::class, 'show'])->name('announcements.show')->middleware('permission:announcements.view');
        Route::get('/iuran', [MemberDueController::class, 'index'])->name('dues.index')->middleware('permission:finance.view');
    });
});
