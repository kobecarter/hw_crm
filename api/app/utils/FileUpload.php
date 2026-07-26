<?php

namespace App\Utils;

use App\Utils\ResponseMessages;

class FileUpload
{

    public static function store($key, $to, $extensions)
    {
        try {
            $save2Dir = rtrim(__DIR__  . '/' . $to);
            if (!is_dir($save2Dir)) {
                throw new \Exception('Le dossier n\'existe pas ', 400);
            }
            $files = request()->files();
            if (!isset($files[$key])) {
                return ['success' => false, 'message' => ResponseMessages::messages('noFileUploaded')];
            }
            $file = $files[$key];
            $name = $file["name"];
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            if (!$ext || !in_array($ext, $extensions)) {
                return ['success' => false, 'message' => ResponseMessages::messages('fileExtensionNotAllowed')];
            }
            $filename = md5(uniqid(rand(), true)) . "." . $ext;
            if (@move_uploaded_file($file["tmp_name"], "$save2Dir/$filename")) {

                if (@chmod("$save2Dir/$filename", 0755)) {
                    return ['success' => true, 'filename' => $filename];
                }
            }
            return ['success' => false, 'message' => ResponseMessages::messages('fileUploadFailed')];
        } catch (\Throwable $th) {
            return ['success' => false, 'message' => $th->getMessage(), 'dir' => $save2Dir];
        }
    }
}
