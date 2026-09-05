<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('categories', 'pages::categories.category-index')->name('categories');
    Route::livewire('categories/{category:category_slug}/{user:name}/checklists', 'pages::check-list.check-list-index')->name('checklists');
});

require __DIR__.'/settings.php';
