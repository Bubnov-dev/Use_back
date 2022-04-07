<?php

namespace App\Models\Client\Abstracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;

class AbstractUser extends Authenticatable
{
    use HasApiTokens;

    /**
     * default find user by email/phone or returns json
     *
     * Here i use die() and give response to for request. I suppose it should not be in a model,
     * but this function is used in many places, and next code shouldn't be executed, if user doesn't exist
     *
     *
     * @param $phone
     * @param null $email
     * @return mixed
     */
    public function findUser($phone, $email = null)
    {
        if ($phone) {
            $user = self::where('phone', $phone);
        } else {
            $user = self::where('email', $email);
        }

        if($user->count()==0){
            die(json_encode([
                'result' => false,
                'message' => 'no such user'
            ]));
        }
        else{
            return $user;
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
            $user->code = null; //todo check, save
            $user->save();
            return [
                'result' => true,
                'token' => $user->createToken('myapptoken')->plainTextToken
            ];
        } else {
            return [
                'result' => false
            ];
        }
    }

}
