<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UtilController extends Controller
{
    public function generatePIN($digits = 4): string
    {
        $i = 0; //counter
        $pin = ""; //our default pin is blank.
        while ($i < $digits) {
            //generate a random number between 0 and 9.
            $pin .= mt_rand(0, 9);
            $i++;
        }
        return $pin;
    }
    public function uploadFile($file, $tmp = true)
    {
        if ($tmp) {
            $path = $file->store('public/files-tmp');

        } else {
            $path = $file->store('public/files');
        }
        return "http://" . $_SERVER['SERVER_NAME'] .':'.$_SERVER['SERVER_PORT']. "/storage/" . $path;
    }
}
