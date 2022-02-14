<?php


namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrService
{
    public function generateQR($url)
    {
        try {
            return $qrFile = QrCode::format('png')
                ->size(720)
                ->encoding('UTF-8')
                ->errorCorrection('L')
                ->generate($url);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }


    function updateGenerateQR($url, $photoUrl)
    {
        try {

            $fitImage = Image::make(env('URL_STORAGE') .$photoUrl)->greyscale();
            $extension = '.' . explode("/", $fitImage->mime())[1];
            $fileName = md5(random_int(1, 10000000) . microtime());
            $storage = Storage::disk('public');
            $storage->put("image/black/$fileName$extension", $fitImage->encode());
            $urlBlack =  "/image/black/$fileName$extension";

            return $qrFile = QrCode::format('png')
                ->size(720)
                ->encoding('UTF-8')
                ->errorCorrection('L')
                ->mergeString(Storage::disk('public')->get($urlBlack))
                ->generate($url);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    public function savePhoto($photo)
    {
        try {
            $name = time();
            $route = "qr/$name.png";
            Storage::disk('public')
                ->put($route, $photo, 'public');
            return $route;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}