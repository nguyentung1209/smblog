<?php

namespace SM\Blog\Helper;

class Helper
{
    public static function convertToURL($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
    public static function uploadImage($file)
    {
        $error = [];
        if (!isset($file)) {
            array_push($error, "Data Invalid");
            die;
        }
//        if ($file['error'] != 0) {
//            array_push($error, "Data Upload Invalid");
//            die;
//        }
        $target_dir = "/pub/blog/";
        $target_file = $target_dir . basename($file["name"]);
        $allowUpload = true;
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        $allowTypes = ['jpg', 'png', 'jpeg', 'gif'];
        if (isset($_POST["submit"])) {
            $check = getimagesize($file["tmp_name"]);
            if ($check !== false) {
                $allowUpload = true;
            } else {
                $allowUpload = false;
            }
        }
        if (file_exists($target_file)) {
            $allowUpload = false;
            array_push($error, "Image Name Already Exist!");
        }
        if (!in_array($imageFileType, $allowTypes)) {
            $allowUpload = false;
            array_push($error, "Image Type Must Be one of JPG, PNG, JPEG, GIF");
        }
        if ($allowUpload) {
//            move_uploaded_file($file["tmp_name"], $target_file);
            return true;
        } else {
            return $error;
        }
    }

}
