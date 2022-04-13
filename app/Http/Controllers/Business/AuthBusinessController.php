<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business\BusinessUser;
use App\Models\Business\BusinessUserOwner;
use App\Models\Business\Company;
use App\Models\Business\CompanyCategory;
use App\Models\Business\Representative;
use Illuminate\Http\Request;

class AuthBusinessController extends Controller
{
    public function registerOwner(Request $request)
    {
        $user = BusinessUser::create([
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'password' => $request->input('password')
        ]);

        $user->save();

        return BusinessUserOwner::create([
            'business_user_id' => $user->id
        ])->save();
    }

    public function addCompany(Request $request)
    {
        $company = Company::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'logo' => $request->input('logo'),
            'company_category_id' => $request->input('company_category_id')
        ]);
        $company->save();
        foreach ($request->input('representatives') as $representative) {
            $_representative = Representative::create([
                'address' => $representative->address,
                'howToGo' => $representative->howToGo,
                'phone' => $representative->phone
            ]);

            $_representative->save();


        }
    }
}
