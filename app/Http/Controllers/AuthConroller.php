<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\User_tmp;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class AuthConroller extends Controller
{

    public function test(){
        return User_tmp::getCode('79006497536');
    }

    private function generatePIN($digits = 4)
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

    public function registerByEmail(Request $request)
    {

    }

    public function registerByPhone(string $phone, string $code)
    {
        UserTmpController::create([
            'phone' => $phone,
            'code' => $code
        ]);
        return $code;

        APIController::sendMessage($phone, 'text');
    }

    public function register(Request $request) //todo or login
    {
        $phone = $request->input('phone');
        $email = $request->input('email');
        $code = self::generatePIN();

        if (User::where('phone', $phone)->orWhere('email', $email)->count()==0) {
            if ($phone) {
                return $this->registerByPhone($phone, $code);
            } else {
                return $this->registerByEmail($email, $code);
            }
        }
        else{
            User::setCode($phone, $email, $code);
            if ($phone) {
                return $code;
                //APIController::sendMessage($phone, $code);
            } else {
                return $this->sendMessageByEmail($email, $code);
            }
        }

    }

    /**
     * @param Request $request
     * @param string $type
     * @return \Illuminate\Http\JsonResponse
     */
    public static function submitCode(Request $request, string $type = 'register')
    {
        $code = $request->input('code');
        $phone = $request->input('phone');
        $email = $request->input('email');
        //todo if give phone && email return error
        if (User::where('phone', $phone)->orWhere('email', $email)->count()==0) {
            $result = User_tmp::loginBySubmitCode($phone, $email, $code);
        }
        else{
            $result = User::loginBySubmitCode($phone, $email, $code);
        }
        return response()->json($result);
    }



    public function submitRegister(Request $request)
    {
        $code = $request->input('code');
        $phone = $request->input('phone');
        $email = $request->input('email');

        //todo if we have few accounts with such email or phone -> error
        $tmp_user = $request->user();

        if ($tmp_user instanceof User_tmp){

            $user = User::create([
                'name' => $request->input('name'),
                'email' => $tmp_user->email!='' ? $tmp_user->email : $request->input('email'),
                'phone' => $tmp_user->phone!='' ? $tmp_user->phone : $request->input('phone'),
            ]);
            $tmp_user->delete();
            $result = $user->save();
            if ($result) {
                return response()
                    ->json(['result' => true, 'token' => $user->createToken('myapptoken')->plainTextToken]);
            }
        }



    }

    private function sendMessageByEmail($email, string $code)
    {
    }

    public function getMe(Request $request){
        return $request->user();
    }

}
