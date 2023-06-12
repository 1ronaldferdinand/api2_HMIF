<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Response;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\RefreshToken;

class UserController extends Controller
{
    public function login(Request $request)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //get credentials from request
        $credentials = $request->only('email', 'password');

        //if auth failed
        if(!$token = auth()->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password Anda salah'
            ], 401);
        }

        $member = new MemberController();
        
        if($member){
            $user = $member->get_member();
        } else {
            //if auth success
            return response()->json([
                'success' => false,
                'user'    => null,   
            ], 401);
        }

        //if auth success
        return response()->json([
            'success' => true,
            'user'    => $user,    
            'token'   => $token
        ], 200)->withCookie(Cookie::make('myToken', $token));
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'nim' => 'required|string|max:255|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'nim' => $request->get('nim'),
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        //return response JSON user is created
        if($user) {
            return response()->json([
                'success' => true,
                'user'    => $user,  
            ], 201);
        }

        //return JSON process insert failed 
        return response()->json([
            'success' => false,
        ], 409);
    }

    public function refresh(Request $request)
    {
        $refreshToken = JWTAuth::refresh(JWTAuth::getToken());

        $user = JWTAuth::setToken($refreshToken)->toUser();

        $request->headers->set('Authorization', 'Bearer ' . $refreshToken);

        return response()->json([
        'success' => true,
        'user' => $user,
        'token' => $refreshToken,
        ], 200);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
