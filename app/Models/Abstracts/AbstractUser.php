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

    /**
     * default find user by email/phone
     *
     * @param $phone
     * @param null $email
     * @return mixed
     */
    public function findUser($phone, $email = null)
    {
        if ($phone) {
            return self::where('phone', $phone);
        } else {
            return self::where('email', $email);
        }
    }

    /**
     * get code of User with findUser
     *
     * @param $phone
     * @param null $email
     * @return mixed
     */
    public function getCode($phone, $email = null)
    {
        $user = self::findUser($phone, $email);
        return $user->select('code')->first()->code;
    }

    /**
     * checks code and return token
     *
     * @param $phone
     * @param $email
     * @param string $code
     * @return array|false[]
     */
    public static function loginBySubmitCode($phone, $email, string $code)
    {
        $user = self::findUser($phone, $email)->first();
        $real_code = self::getCode($phone, $email);

        if ($code === $real_code) {
            $user->code = ""; //todo check, save
            $user->save();
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
