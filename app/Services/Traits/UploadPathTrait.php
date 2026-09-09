<?php

namespace App\Services\Traits;

trait UploadPathTrait
{
    public function getUploadPath($folder, $name = null)
    {
        $folderPath = 'uploads/';
        if ($name) {
            $name = preg_replace('/[^\w\s-]+|\s+/', '-', strtolower($name));
            $name = preg_replace('/-+/', '-', $name);  // Merge consecutive hyphens
            $name = trim($name, '-');  // Trim hyphens

            return "$folderPath$folder/$name";
        }

        return "$folderPath$folder";
    }
}
