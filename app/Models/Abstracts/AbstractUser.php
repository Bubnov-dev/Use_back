<?php

namespace App\Models\Abstracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;

class AbstractUser extends Authenticatable
{
    use HasApiTokens;

    public function findUser($phone, $email = null)
    {
        if ($phone) {
            return self::where('phone', $phone);
        } else {
            return self::where('email', $email);
        }
    }

    public function getCode($phone, $email = null)
    {
        $user = self::findUser($phone, $email);
        return $user->select('code')->first()->code;
    }

    public static function loginBySubmitCode($phone, $email, string $code)
    {
        $user = self::findUser($phone, $email)->first();
        $real_code = self::getCode($phone, $email);
        Log::info('code: '.$code);

        Log::info('code: '.$real_code);
        if ($code === $real_code) {
            $user->code = "";
            return [
                'login' => true,
                'token' => $user->createToken('myapptoken')->plainTextToken
            ];
        } else {
            return [
                'login' => false
            ];
        }
    }

}
