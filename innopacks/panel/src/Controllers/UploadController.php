<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoShop\Panel\Controllers;

use Illuminate\Http\JsonResponse;
use InnoShop\Panel\Requests\UploadFileRequest;
use InnoShop\Panel\Requests\UploadImageRequest;
use App\Http\Controllers\ImageController;
use Exception;

class UploadController
{
    /**
     * Upload images.
     *
     * @param  UploadImageRequest  $request
     * @return JsonResponse
     */
    
    
     public function images(UploadImageRequest $request): JsonResponse
     {
         try {
             $type = $request->input('type', 'common');
 
             // Panggil ImageController untuk mengoptimalkan gambar dan simpan ke folder yang diinginkan
             $imageController = new ImageController();
             $optimizedImageResponse = $imageController->uploadAndOptimize($request, "catalog/$type");
 
             // Ambil path gambar dari respons JSON yang dikembalikan oleh ImageController
             $optimizedImagePath = json_decode($optimizedImageResponse->getContent(), true)['path'];
 
             // Simpan path gambar yang dioptimalkan
             $data = [
                 'url' => asset($optimizedImagePath),
                 'value' => $optimizedImagePath,
             ];
 
             return json_success('上传成功', $data);
         } catch (Exception $e) {
             // Tangani error jika terjadi masalah
             return response()->json(['error' => $e->getMessage()], 500);
         }
     }
    // public function images(UploadImageRequest $request): JsonResponse
    // {
    //     $image    = $request->file('image');
    //     $type     = $request->file('type', 'common');
    //     $filePath = $image->store("/{$type}", 'catalog');
    //     $realPath = "catalog/$filePath";

    //     $data = [
    //         'url'   => asset($realPath),
    //         'value' => $realPath,
    //     ];

    //     return json_success('上传成功', $data);
    // }
    /**
     * Upload document files
     *
     * @param  UploadFileRequest  $request
     * @return JsonResponse
     */
    public function files(UploadFileRequest $request): JsonResponse
    {
        $file     = $request->file('file');
        $type     = $request->file('type', 'files');
        $filePath = $file->store("/{$type}", 'catalog');
        $realPath = "catalog/$filePath";

        $data = [
            'url'   => asset($realPath),
            'value' => $realPath,
        ];

        return json_success('上传成功', $data);
    }
}