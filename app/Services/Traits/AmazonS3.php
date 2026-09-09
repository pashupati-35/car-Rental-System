<?php

namespace App\Services\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait AmazonS3
{
    protected $disk = 'wasabi';

    public function uploadToS3($uploadPath, $realPath, $thumbPath, $fileName, $visibility)
    {
        setStorageConfig();
        $storageType = getStorageType();
        $s3 = Storage::disk($storageType);

        $uploadPathReal = "$uploadPath/$fileName";
        $uploadPathThumb = "$uploadPath/thumb/$fileName";

        $uploadPathReal = buildUploadPathUrl($uploadPathReal);
        $uploadPathThumb = buildUploadPathUrl($uploadPathThumb);

        if (! empty($realPath) && is_file($realPath)) {
            $s3->put($uploadPathReal, \File::get($realPath), $visibility);
        }
        if (! empty($thumbPath) && is_file($thumbPath)) {
            $s3->put($uploadPathThumb, \File::get($thumbPath), $visibility);
        }

        if (is_file($realPath)) {
            \File::delete($realPath);
        }

        if (is_file($thumbPath)) {
            \File::delete($thumbPath);
        }

        return $s3->url($uploadPathReal);
    }

    public function deleteFromS3($uploadPath, $imageName)
    {
        setStorageConfig();
        $storageType = getStorageType();
        $s3 = Storage::disk($storageType);
        $path = [];
        $parentPath = "$uploadPath/$imageName";
        $thumbPath = "$uploadPath/thumb/$imageName";

        $parentPath = buildUploadPathUrl($parentPath);
        $thumbPath = buildUploadPathUrl($thumbPath);

        if (! empty($parentPath)) {
            array_push($path, $parentPath);
        }

        if (! empty($thumbPath)) {
            array_push($path, $thumbPath);
        }
        if (count($path) > 0) {
            $s3->delete($path);
        }

        return true;
    }

    public function createThumbS3($file, $width = 320, $height = 320)
    {
        $imageFile = \Image::make($file)->resize($width, $height)->stream();
        $imageFile = $imageFile->__toString();

        return $imageFile;
    }

    public function deleteFileFromS3($path)
    {
        setStorageConfig();
        $storageType = getStorageType();
        $s3 = Storage::disk($storageType);
        $parentPath = $path.'/'.$path;
        if ($s3->exists($parentPath)) {
            return $s3->readAndDelete($parentPath);
        }
    }

    public function setFolderPermission()
    {
        setStorageConfig();
        $storageType = getStorageType();
        $s3 = Storage::disk($storageType);
        $directoryArray = $s3->allDirectories();
        if (count($directoryArray) > 0) {
            foreach ($directoryArray as $index => $dirPath) {
                $s3->setVisibility($dirPath, 'public');
            }
        }

        return true;
    }

    public function setFilePermission()
    {
        setStorageConfig();
        $storageType = getStorageType();
        $s3 = Storage::disk($storageType);
        $fileArray = $s3->allFiles();
        foreach ($fileArray as $i => $file) {
            if (! Str::contains($file, '.jpeg') && ! Str::contains($file, '.png') && ! Str::contains($file, '.jpg')) {
                unset($fileArray[$i]);
            }
        }
        if (count($fileArray) > 0) {
            foreach ($fileArray as $index => $filePath) {
                $s3->setVisibility($filePath, 'public');
            }
        }

        return true;
    }

    public function listFilesAndFolder($path, $type = 'files')
    {
        setStorageConfig();
        $storageType = getStorageType();
        $s3 = Storage::disk($storageType);
        if ($type == 'files') {
            if ($path) {
                return $s3->allFiles($path);
            }

            return $s3->allFiles();
        } else {
            return $s3->allDirectories();
        }
    }

    public function deleteAllFiles($path)
    {
        setStorageConfig();
        $storageType = getStorageType();
        $s3 = Storage::disk($storageType);
        $paths = $s3->allFiles($path);

        return $s3->delete($paths);
    }
}
