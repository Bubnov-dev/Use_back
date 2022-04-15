<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\APIMailController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilController;
use App\Http\Requests\RegisterBusinessRequest;
use App\Models\Business\BusinessUser;
use App\Models\Business\BusinessUserOwner;
use App\Models\Business\Company;
use App\Models\Business\CompanyCategory;
use App\Models\Business\Representative;
use App\Models\Business\WorkTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthBusinessController extends Controller
{
    public function registerOwner(RegisterBusinessRequest $request)
    {
        $email_hash = UtilController::generatePIN(10);

        $user = BusinessUser::create([
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'password' => Hash::make($request->input('password')),
            'email_hash' => $email_hash
        ]);

        $user->save();

        BusinessUserOwner::create([
            'business_user_id' => $user->id
        ])->save();

        APIMailController::sendMessage($request->input('email'), "Подтверждение регистрации, перейдите по ссылке " . $_SERVER['SERVER_NAME'] . '/verifyEmail/' .($email_hash));

        return json_encode([
            'token' => $user->createToken($request->input('phone'))->plainTextToken,
            'id' => $user->id
        ]);
    }

    public function addCompany(Request $request)
    {
        $company = Company::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'logo' => $request->input('logo'),
            'company_category_id' => $request->input('company_category_id'),
            'business_user_id' => $request->input('business_user_id')
        ]);
        $company->save();
        foreach ($request->input('representatives') as $representative) {
            $_representative = Representative::create([
                'type' => $representative['type'],
                'address' => $representative['address'],
                'howToGo' => $representative['howToGo'],
                'phone' => $representative['phone'],
                'company_id' => $company->id
            ]);
            $_representative->save();

            foreach ($representative['days'] as $day) {
                WorkTime::create([
                    'day' => $day['day_key'],
                    'open' => $day['hour_open'],
                    'close' => $day['hour_close'],
                    'representative_id' => $_representative->id,
                ]);
            }

        }
    }

    public function verifyEmail($email_hash)
    {
        $user = BusinessUser::where('email_hash', $email_hash)->first();
        if ($user){
            $user->email_verified_at =  new \DateTime();
            $user->save();
            return "thanks";
        }
        else return "no...";

    }
}
