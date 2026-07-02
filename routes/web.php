<?php

use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\AdministrativeUnitController;
use App\Http\Controllers\Backend\ApartmentController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\ContentController;
use App\Http\Controllers\Backend\CustomerInquiryController as BackendCustomerInquiryController;
use App\Http\Controllers\Backend\MenuController;
use App\Http\Controllers\Backend\PageController as BackendPageController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\SeoConfigController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Frontend\CustomerInquiryController as FrontendCustomerInquiryController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('frontend.home');
Route::get('/gioi-thieu', [PageController::class, 'about'])->name('frontend.about');
Route::get('/lien-he', [PageController::class, 'contact'])->name('frontend.contact');
Route::get('/danh-muc-san-pham/{slug}', function (string $slug) {
    return redirect()->route('frontend.categories.show', $slug);
})->name('frontend.products.category');
Route::get('/san-pham/{slug}', [PageController::class, 'legacyProductShow'])->name('frontend.products.show.legacy');
Route::get('/tin-tuc', [PageController::class, 'newsIndex'])->name('frontend.news.index');
Route::get('/danh-muc-tin-tuc/{slug}', function (string $slug) {
    return redirect()->route('frontend.categories.show', $slug);
})->name('frontend.news.category');
Route::get('/tin-tuc/{slug}', [PageController::class, 'legacyNewsShow'])->name('frontend.news.show.legacy');
Route::get('/login', function () {
    return redirect()->route('backend.admin.login');
})->name('login');
Route::post('/customer-inquiries', [FrontendCustomerInquiryController::class, 'store'])->name('frontend.customer-inquiries.store');

Route::prefix('admin')->name('backend.')->group(function () {
    Route::name('admin.')->group(function () {
        Route::get('login', [AdminController::class, 'login'])->name('login');
        Route::post('login', [AdminController::class, 'authenticate'])->name('authenticate');
        Route::post('logout', [AdminController::class, 'logout'])->name('logout');
    });

    Route::middleware('auth')->group(function () {
        Route::name('admin.')->group(function () {
            Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        });

        Route::resource('categories', CategoryController::class)
            ->except(['show'])
            ->names('categories');
        Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])
            ->name('categories.toggle-status');

        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ContentController::class, 'productIndex'])->name('index');
            Route::get('create', [ContentController::class, 'productCreate'])->name('create');
            Route::post('/', [ContentController::class, 'productStore'])->name('store');
            Route::get('{post}/edit', [ContentController::class, 'productEdit'])->name('edit');
            Route::put('{post}', [ContentController::class, 'productUpdate'])->name('update');
            Route::delete('{post}', [ContentController::class, 'productDestroy'])->name('destroy');
            Route::patch('{post}/toggle-status', [ContentController::class, 'productToggleStatus'])->name('toggle-status');
            Route::patch('{post}/toggle-featured', [ContentController::class, 'productToggleFeatured'])->name('toggle-featured');
        });

        Route::prefix('news')->name('news.')->group(function () {
            Route::get('/', [ContentController::class, 'newsIndex'])->name('index');
            Route::get('create', [ContentController::class, 'newsCreate'])->name('create');
            Route::post('/', [ContentController::class, 'newsStore'])->name('store');
            Route::get('{post}/edit', [ContentController::class, 'newsEdit'])->name('edit');
            Route::put('{post}', [ContentController::class, 'newsUpdate'])->name('update');
            Route::delete('{post}', [ContentController::class, 'newsDestroy'])->name('destroy');
            Route::patch('{post}/toggle-status', [ContentController::class, 'newsToggleStatus'])->name('toggle-status');
        });

        Route::resource('apartments', ApartmentController::class)
            ->except(['show'])
            ->names('apartments');

        Route::prefix('administrative-units')->name('administrative-units.')->group(function () {
            Route::get('provinces', [AdministrativeUnitController::class, 'provinces'])->name('provinces');
            Route::get('wards', [AdministrativeUnitController::class, 'wards'])->name('wards');
        });

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingController::class, 'edit'])->name('edit');
            Route::put('/', [SettingController::class, 'update'])->name('update');
        });

        Route::prefix('seo')->name('seo.')->group(function () {
            Route::get('/', [SeoConfigController::class, 'edit'])->name('edit');
            Route::put('/', [SeoConfigController::class, 'update'])->name('update');
        });

        Route::resource('menus', MenuController::class)
            ->except(['show'])
            ->names('menus');
        Route::patch('menus/{menu}/toggle-status', [MenuController::class, 'toggleStatus'])
            ->name('menus.toggle-status');
        Route::patch('menus/{menu}/sort-order', [MenuController::class, 'updateSortOrder'])
            ->name('menus.update-sort-order');

        Route::resource('users', UserController::class)
            ->except(['show'])
            ->names('users');

        Route::resource('pages', BackendPageController::class)
            ->except(['show'])
            ->names('pages');
        Route::patch('pages/{page}/toggle-status', [BackendPageController::class, 'toggleStatus'])
            ->name('pages.toggle-status');

        Route::get('customer-inquiries', [BackendCustomerInquiryController::class, 'index'])
            ->name('customer-inquiries.index');

        Route::post('uploads/editor-image', [ContentController::class, 'uploadEditorImage'])
            ->name('admin.uploads.editor-image');
    });
});

Route::get('/{categorySlug}/{slug}', [PageController::class, 'contentShow'])->name('frontend.content.show');
Route::get('/{slug}', [PageController::class, 'categoryBySlug'])->name('frontend.categories.show');
