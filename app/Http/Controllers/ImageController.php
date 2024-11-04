<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class ImageController extends Controller
{
    public function uploadAndOptimize(Request $request, $targetFolder = 'uploads/optimized')
    {
        // Validasi file upload
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120', // Maks 5MB
        ]);

        $imageFile = $request->file('image');
        $fileName = time() . '_' . $imageFile->getClientOriginalName();
        $path = public_path("$targetFolder/$fileName");

        // Pastikan direktori tujuan ada
        if (!file_exists(public_path($targetFolder))) {
            mkdir(public_path($targetFolder), 0777, true);
        }

        // Proses optimasi dan simpan gambar
        $image = Image::make($imageFile)
                    ->resize(800, 800, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->save($path);

        // Return path relatif
        return response()->json([
            'status' => 'success',
            'path' => "$targetFolder/$fileName",
        ]);
    }

}
