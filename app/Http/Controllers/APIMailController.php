<?php

namespace App\Http\Controllers;


use App\Mail\CodeMail;
use Illuminate\Support\Facades\Mail;

class APIMailController
{
    /**
     * @param $email
     * @param $text
     * @return bool
     */
    public function sendMessage($email, $text){
        Mail::to($email)->send(new CodeMail($text));
        return true;
    }
}
