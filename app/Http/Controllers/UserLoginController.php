<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserLoginController extends Controller
{

    public function login(Reqeust $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
    }
    public function logout(Reqeust $request)
    {
        //
    }
}
