<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterLoginRequest;
use App\Http\Requests\RegisterSubmitRequest;
use App\Http\Requests\submitCodeRequest;
use App\Mail\CodeMail;
use App\Models\User;
use App\Models\User_tmp;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AuthConroller extends Controller
{

    public function test(RegisterSubmitRequest $request)
    {
        return $request;
    }

    /**
     * @param int $digits
     * @return string
     */
    private function generatePIN($digits = 4): string
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

    /**
     * creating User_tmp by email and sending email with code
     *
     * @param string $email
     * @param string $code
     * @return bool
     */
    public function registerByEmail(string $email, string $code): bool
    {
        UserTmpController::create([
            'email' => $email,
            'code' => $code
        ]);

        return APIMailController::sendMessage($email, $code);
    }

    /**
     * creating user_tmp by phone and sending code by sms
     *
     * @param string $phone
     * @param string $code
     * @return \Illuminate\Http\Client\Response
     */
    public function registerByPhone(string $phone, string $code)
    {
        UserTmpController::create([
            'phone' => $phone,
            'code' => $code
        ]);

        return APISMSController::sendMessage($phone, $code);
    }

    /**
     * get phone/email and send code to User/new User_tmp
     *
     * @param Request $request
     * @return bool|\Illuminate\Http\Client\Response
     */
    public function registerLogin(RegisterLoginRequest $request)
    {
        $phone = $request->input('phone');
        $email = $request->input('email');
        $code = self::generatePIN();

        if (User::where('phone', $phone)->orWhere('email', $email)->count() == 0) { // register
            if ($phone) {
                return $this->registerByPhone($phone, $code);
            } else {
                return $this->registerByEmail($email, $code);
            }
        } else { // login
            User::setCode($phone, $email, $code);
            if ($phone) {
                return APISMSController::sendMessage($phone, $code);
            } else {
                return APIMailController::sendMessage($email, $code);
            }
        }

    }

    /**
     * get phone/email, code and give new token to User/new User_tmp

     *
     * @param Request $request
     * @param string $type
     * @return \Illuminate\Http\JsonResponse
     */
    public static function submitCode(SubmitCodeRequest $request)
    {
        $code = $request->input('code');
        $phone = $request->input('phone');
        $email = $request->input('email');
        //todo if give phone && email return error
        if (User::where('phone', $phone)->orWhere('email', $email)->count() == 0) {
            $result = User_tmp::loginBySubmitCode($phone, $email, $code);
        } else {
            $result = User::loginBySubmitCode($phone, $email, $code);
        }
        return response()->json($result);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function submitRegister(RegisterSubmitRequest $request)
    {
        //todo if we have few accounts with such email or phone -> error
        $tmp_user = $request->user();

        if ($tmp_user instanceof User_tmp) {

            $user = User::create([
                'name' => $request->input('name'),
                'email' => $tmp_user->email != '' ? $tmp_user->email : $request->input('email'),
                'phone' => $tmp_user->phone != '' ? $tmp_user->phone : $request->input('phone'),
            ]);
            $tmp_user->delete();
            $result = $user->save();
            if ($result) {
                return response()
                    ->json(['result' => true, 'token' => $user->createToken('myapptoken')->plainTextToken]);
            }
        }


    }

    //todo delete, it's for test
    public function getMe(Request $request)
    {
        return $request->user();
    }

}
