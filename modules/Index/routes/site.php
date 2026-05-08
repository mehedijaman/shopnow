<?php

use Illuminate\Support\Facades\Route;
use Modules\Index\Http\Controllers\IndexController;
use Modules\Index\Http\Controllers\RobotsController;
use Modules\Index\Http\Controllers\SitemapController;

Route::get('/', [IndexController::class, 'index'])->name('site.index');
Route::get('/about', [IndexController::class, 'about'])->name('site.about');
Route::get('/privacy-policy', [IndexController::class, 'privacyPolicy'])->name('site.privacyPolicy');
Route::get('/terms-of-service', [IndexController::class, 'termsOfService'])->name('site.termsOfService');
Route::get('/refund-policy', [IndexController::class, 'refundPolicy'])->name('site.refundPolicy');

// Dynamic robots.txt (overrides the static public/robots.txt via routing)
Route::get('/robots.txt', RobotsController::class)->name('robots.txt');

// Sitemaps
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap-static.xml', [SitemapController::class, 'staticPages'])->name('sitemap.static');
Route::get('/sitemap-products.xml', [SitemapController::class, 'products'])->name('sitemap.products');
Route::get('/sitemap-blog.xml', [SitemapController::class, 'blog'])->name('sitemap.blog');
