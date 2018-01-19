<?php

namespace AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageService extends Controller
{

    public function imageSaveAction(UploadedFile $uploadedFile, $name, $targetDir)
    {
        if ($name) {
            // replace non letter or digits by -
            $name = preg_replace('~[^\pL\d]+~u', '-', $name);

            // transliterate
            $name = iconv('utf-8', 'us-ascii//TRANSLIT', $name);

            // remove unwanted characters
            $name = preg_replace('~[^-\w]+~', '', $name);

            // trim
            $name = trim($name, '-');

            // remove duplicate -
            $name = preg_replace('~-+~', '-', $name);

            // lowercase
            $name = strtolower($name);

            $name = $name . '.' . $uploadedFile->guessExtension();

            $uploadedFile->move(
                $targetDir,
                $name
            );
        }

        return $name;
    }
}