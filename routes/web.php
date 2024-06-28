<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Demo\DemoController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Home\HomeSliderController;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/test-email', function () {
    $details = [
        'title' => 'Test Email from Laravel',
        'body' => 'This is a test email sent from Laravel application'
    ];

    Mail::raw($details['body'], function ($message) use ($details) {
        $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                ->to('test@example.com')
                ->subject($details['title']);
    });

    return 'Email sent!';
});

Route::get('/', function () {
    //$manager = new ImageManager(new Driver());
    //$image = $manager->read('images/example.jpg');
    return view('frontend.index');
});

Route::controller(DemoController::class) -> group(function() {
    Route::get('/about', 'Index') -> name('about.page') -> middleware('check');
    Route::get('/contact', 'ContactMethod') -> name('contact.page'); //name routing
});

/*
Route::get('/about', [DemoController::class, 'Index']); //loaded from DemoController.php
Route::get('/contact', [DemoController::class, 'ContactMethod']); */

/*
Route::get('/about', function () { //loaded directly from web.php
    return view("about");
});

Route::get('/contact', function () {
    return view("contact");
});
*/

Route::get('/dashboard', function () {
    return view('admin.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// All Admin Routes
Route::controller(AdminController::class) -> group(function() {
    Route::get('/admin/logout', 'destroy') -> name('admin.logout');
    Route::get('/admin/login', 'displayLogin') -> name('admin.login');
    Route::get('/admin/register', 'displayRegister') -> name('admin.register');
    Route::get('/admin/recover', 'displayRecover') -> name('admin.recover');
    Route::get('/admin/profile', 'profile') -> name('admin.profile');
    Route::get('/edit/profile', 'editProfile') -> name('edit.profile');
    Route::get('/change/password', 'changePassword') -> name('change.password');
    Route::post('/update/password', 'updatePassword') -> name('update.password');
    Route::post('/store/profile', 'storeProfile') -> name('store.profile');
    Route::post('/admin/login/send', 'storeLogin') -> name('admin.login.send');
    Route::post('/admin/register/send', 'storeRegister') -> name('admin.register.send');
    Route::post('/admin/recover/send', 'storeRecover') -> name('admin.recover.send');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// All Home Slide Routes

Route::controller(HomeSliderController::class) -> group(function() {
    Route::get('/home/slide', 'homeSlider') -> name('home.slide');
    Route::post('/update/slider', 'updateSlider') -> name('update.slider');
});
