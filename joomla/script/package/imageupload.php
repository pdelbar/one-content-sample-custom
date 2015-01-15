<?php

//------------------------------------------------------------------
// package request : functions to access the request
//------------------------------------------------------------------

class oneScriptPackageImageupload extends One_Script_Package {

    //get uploaded images, resize, save and return image paths
    public function getUploadedImages($basepath = '', $modelId = 0, $curImages = array(), $imageParams = array()) {
        $absolutepath = constant('JPATH_SITE') . $basepath;

        $imageParams = array_merge(array('height' => 0, 'width' => 0), $imageParams);

        $width = intval($imageParams['width']);
        $height = intval($imageParams['height']);

        $imagePaths = array();
        
        foreach ($_FILES as $fieldname => $file) {
			

            if ($file['type'] == 'image/jpeg' || $file['type'] == 'image/gif' || $file['type'] == 'image/png') {

                if ($modelId == 0 || !array_key_exists($fieldname, $curImages) || $curImages[$fieldname] == '' || $curImages[$fieldname] != $basepath . $file['name']) {

                    $uploadImage = false;

                    $filename = self::getFileName($absolutepath, 'tmp_' . $file['name']);
					$filepath = $absolutepath . $filename;


                    if (JFile::upload($file['tmp_name'], $filepath)) {
                        $uploadImage = self::resizeImage($absolutepath, $file['name'], $filename, $width, $height);
                        JFile::delete($filepath);
                    } else die( '<br>boo');

                    if ($uploadImage)
                        $imagePaths[$fieldname] = $basepath . $uploadImage;
                }
            }
        }
        return $imagePaths;
    }

    //get valid, non existing filename
    public static function getFileName($basepath, $filename) {

        //return filename if file doesn't exist
        if (!file_exists($basepath . $filename))
            return $filename;

        //create new filename with suffix until unexisting file is found	
        $fileparts = explode('.', $filename);
        $extension = '.' . array_pop($fileparts);
        $filename = implode('.', $fileparts);
        $suffix = 1;

        while (file_exists($basepath . $filename . '_' . $suffix . $extension))
            $suffix++;

        return $filename . '_' . $suffix . $extension;
    }

    //resize submitted image
    public static function resizeImage($basepath, $basefilename, $filename, $width, $height) {

        $currImage = self::getImageIdentifier($basepath . $filename);
        $writtenImage = false;

        if ($currImage && ($width > 0 || $height > 0)) {

            $currWidth = intval(imagesx($currImage));
            $currHeight = intval(imagesy($currImage));

            if ($currWidth && $currHeight) {
                if ($width > 0 && $height <= 0) {
                    $imagescale = $width / $currWidth;
                    $height = intval($currHeight * $imagescale);
                } elseif ($width <= 0 && $height > 0) {
                    $imagescale = $height / $currHeight;
                    $width = intval($currWidth * $imagescale);
                }

                if ($width > 0 && $height > 0) {


                    $resizedImage = imagecreatetruecolor($width, $height);
                    imagecopyresampled($resizedImage, $currImage, 0, 0, 0, 0, $width, $height, $currWidth, $currHeight);
                    imagedestroy($currImage);
                    $writtenImage = self::writeImage($basepath, $basefilename, $resizedImage);
                }
            }
        }

        return $writtenImage;
    }

    //return image resource identifier or false
    public static function getImageIdentifier($filepath) {

        $extension = strtolower(end(explode('.', $filepath)));

        if ($extension == 'jpg' || $extension == 'jpeg')
            return imagecreatefromjpeg($filepath);
        elseif ($extension == 'gif')
            return imagecreatefromgif($filepath);
        elseif ($extension == 'png')
            return imagecreatefrompng($filepath);

        return false;
    }

    //write image to file, return filename or false
    public static function writeImage($filepath, $filename, $image) {

        $extension = strtolower(end(explode('.', $filename)));
        $filename = self::getFileName($filepath, $filename);


        if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'png') {


            if ($extension == 'jpg' || $extension == 'jpeg')
                imagejpeg($image, $filepath . $filename, 100);
            elseif ($extension == 'gif')
                imagegif($image, $filepath . $filename);
            else
                imagepng($image, $filepath . $filename);
        }

        if (file_exists($filepath . $filename))
            return $filename;
        else
            return false;
    }

}
