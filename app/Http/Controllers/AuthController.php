<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public $successStatus = 200;

    /**
     * Kullanıcı Oluşturma
     *
     * @param [string] name
     * @param [string] email
     * @param [string] password
     * @return [string] message
     */

    public function register(Request $request)
    {
        $request->validate
        ([
            'name'=> 'required|string',
            'email'=> 'required|string|email|unique:users',
            'password'=> 'required|string|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $message ['success'] = 'Kullanıcı Başarıyla Oluşturuldu';
        return response()->json
        ([
            'message'=>$message
        ],201);
    }

    /**
     * Kullanıcı Girişi ve token oluşturma
     *
     * @param [string] email
     * @param [string] password
     * @return [string] token
     * @return [string] token_type
     * @return [string] expires_at
     * @return [string] success
     */

    public function login(Request $request)
    {
        $request->validate
        ([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $credentials = request(['email','password']);
        if(Auth::attempt($credentials))
        {
            $user = Auth::user();
            $message['token'] = $user->createToken('Application')->accessToken;
            $message['token_type'] = 'Bearer';
            $message['experies_at'] = Carbon::parse(Carbon::now()->addWeeks(1))->toDateTimeString();
            $message['success'] = 'Kullanıcı Başarıyla Giriş Yaptı';

            return response()->json
            (['message' => $message], $this->successStatus);
        }
        else
        {
            return response()->json(['error'=>'Giriş Yapılamadı'], 401);
        }
    }
}
