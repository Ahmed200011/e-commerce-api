<?php

namespace App\Traits;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

trait UploadImageTrait {
    public function uploadImage($file, $path, $image_name ,$width, $height) {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);

        // Resize and save the image
        $image->resize($width, $height);
        $image->save(public_path($path) . $image_name);


    }


     public function deleteImage(string $path, string $fileName): void
    {
        $fullPath = public_path($path . $fileName);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
