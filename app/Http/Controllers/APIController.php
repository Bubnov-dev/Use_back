<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class APIController extends Controller
{

    public function sendMessage($phone, $text){
        return $response = Http::get('https://api.iqsms.ru/messages/v2/send', [
            'login' => 'z1597935568350',
            'password' => '528374',
            'phone' => $phone,
            'text' => $text
        ]);
    }


}
