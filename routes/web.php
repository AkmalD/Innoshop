<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

 use Illuminate\Support\Facades\Route; // Tambahkan ini
 use App\Http\Controllers\ImageController;
 
 Route::get('/upload-form', function () {
    return view('upload');
});

Route::post('/upload-image', [ImageController::class, 'uploadAndOptimize'])->name('upload-image');
 
