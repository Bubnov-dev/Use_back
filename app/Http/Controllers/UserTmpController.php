<?php

namespace App\Http\Controllers;

use App\Models\User_tmp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserTmpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(array $fields)
    {
//        if (!count(User_tmp::where('phone', $phone)->get())){
            return User_tmp::create([
                'phone' => ($fields['phone'] ?? ''),
                'code' => ($fields['code'] ?? ''),
                'email' => ($fields['email'] ?? ''),
            ])->save();
//        }
//        else{
//            return User_tmp::where('phone', $phone)->update([
//                'code' => $code,
//            ]);
//        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show($phone)
    {
        return User_tmp::where('phone', $phone)->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User_tmp  $user_tmp
     * @return \Illuminate\Http\Response
     */
    public function edit(User_tmp $user_tmp)
    {
        //
    }

    /**
     * Update the specified resource in storage. name & email & phone
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update($phone, $name = null, $email = null)
    {
        return User_tmp::where('phone', $phone)->update([
            'phone' => $phone,
            'name' => $name,
            'email' => $email,
        ]);
    }

    /**
     * Update the specified resource in storage. name & email & phone
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateCode($phone, $code)
    {
        return User_tmp::where('phone', $phone)->update([
            'phone' => $phone,
            'code' => $code,
        ])  ;
    }

//    public function getCode($phone, $email){
//        if ($phone){
//            return User_tmp::where('phone', $phone)->select('code')->first()->code;
//        }
//        else {
//            return User_tmp::where('email', $email)->select('code')->first()->code;
//        }
//    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $phone
     * @param string $email
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $phone, string $email)
    {
        User_tmp::where('phone', $phone)->orWhere('email', $email)->delete();
    }
}
