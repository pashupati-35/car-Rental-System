<?php

namespace App\Services;

use App\Services\Traits\AmazonS3;
use App\Services\Traits\UploadPathTrait;
use Exception;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

abstract class Service
{
    use AmazonS3, UploadPathTrait;

    protected $uploadPath;

    protected $images;

    public function uploadFile($file, $uploadFor, $uploadName = null, $visibility = 'public', $width = 320, $height = 320)
    {
        if (isset($file) && ! empty($file)) {
            $uploadPath = $this->getUploadPath($uploadFor, $uploadName);

            return $this->uploadFileAndImages($file, $uploadPath, $width, $height, $visibility);
        }
    }

    public function renameFile($uploadFor, $name, $newName)
    {
        try {
            $uploadPath = $this->getUploadPath($uploadFor, $name);
            $pattern = "/$name/";
            $newPath = preg_replace($pattern, $newName, $uploadPath);
            rename(public_path($uploadPath), public_path($newPath));

            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function deleteFile($uploadFor, $fileName, $uploadName = null)
    {
        try {
            $uploadPath = $this->getUploadPath($uploadFor, $uploadName);
            $imageFullPath = "$uploadPath/$fileName";
            $imageThumbFullPath = "$uploadPath/thumb/$fileName";
            if (is_file($imageFullPath)) {
                unlink($imageFullPath);
            }
            if (is_file($imageThumbFullPath)) {
                unlink($imageThumbFullPath);
            }

            if (config('app.env') != 'local') {
                $this->deleteFromS3($uploadPath, $fileName);
            }

            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function uploadFileAndImages(UploadedFile $file, $uploadPath, $width, $height, $visibility)
    {
        $imageType = ['jpeg', 'jpg', 'png', 'ico', 'webp', 'svg', 'gif', 'bmp', 'tiff', 'tif', 'heic', 'heif', 'avif', 'jfif', 'pjpeg', 'pjp'];
        $storageType = getStorageType();

        if (! is_dir('uploads')) {
            mkdir('uploads', 0775, true);
        }

        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        $isImage = true;
        $destination = $uploadPath;
        $file_type = $file->extension();

        $fileName = $file->hashName();
        $file_type = $file->extension();
        $newFileName = sprintf('%s.%s', sha1($fileName).time(), $file_type);

        if (! in_array(strtolower($file_type), $imageType)) {
            $isImage = false;
        }
        if ($isImage) {
            if ($file->isValid()) {
                switch ($file_type) {
                    case 'ico':
                        $file->move($destination, $newFileName);
                        $fileName = $newFileName;
                        break;

                    case 'svg':
                        $file->move($destination, $newFileName);

                        if (! is_dir("$destination/thumb")) {
                            mkdir("$destination/thumb", 0755, true);
                        }
                        copy(
                            "$destination/$newFileName",
                            "$destination/thumb/$newFileName"
                        );

                        $fileName = $newFileName;
                        break;
                    default:
                        $manager = new ImageManager(new Driver);

                        $image = $manager->read($file);
                        $image->save("$destination/$newFileName", 60);
                        $image = new File("$destination/$newFileName");
                        if (substr($file->getClientMimeType(), 0, 5) == 'image' && $file_type != 'ico') {
                            $this->createThumb($image, $width, $height);
                        }
                        $fileName = $newFileName;
                        break;
                }

                if ($storageType && config('app.env') != 'local') {
                    $realPath = "$uploadPath/$fileName";
                    $thumbPath = "$uploadPath/thumb/$fileName";
                    $this->uploadToS3($uploadPath, $realPath, $thumbPath, $fileName, $visibility);
                }

                return $fileName;
            }
        } else {
            $fileName = $newFileName;
            $file->move($destination, $fileName);
            if ($storageType && config('app.env') != 'local') {
                $realPath = "$uploadPath/$fileName";
                $thumbPath = "$uploadPath/thumb/$fileName";
                $this->uploadToS3($uploadPath, $realPath, $thumbPath, $fileName, $visibility);
            }

            return $fileName;
        }

        return null;
    }

    public function createThumb(File $file, $width = 320, $height = 320)
    {
        try {
            $manager = new ImageManager(new Driver);

            $img = $manager->read($file);
            $img->resize($width, $height);
            $path = sprintf('%s/thumb/%s', $file->getPath(), $file->getFilename());
            $directory = sprintf('%s/thumb', $file->getPath());
            if (! file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            $img->save($path);
        } catch (Exception $ex) {
            return null;
        }
    }
}
