<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request){

        $validator = validator($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');
        $token = auth()->attempt($credentials);

        if(!$token){
            return response()->json(['message' => 'Informations de connexion incorrectes'], 401);
        }

        return response()->json(['access_token' => $token, 
        'token_type' => 'bearer',
        'user' =>auth()->user(),
        'expires_at' => env("JWT_TTL") * 60 ], 200);

}

public function register(Request $request){
    $validator = validator($request->all(), [
        'email' =>'required|email|unique:users',
        'password' => 'required|min:8|confirmed',
        'prenom' =>'required',
        'nom' =>'required',
        'nom' =>'required |minlength:2',
        'password' => 'required|min:8|confirmed'
    ]);

    if($validator->fails()){
        return response()->json($validator->errors(), 422);
    }

    $user = User::create([
        'email' => $request->email,
        'password' => Hash::make($request->password)
    ]);

    $token = auth()->login($user);

    return response()->json(['access_token' => $token, 
    'token_type' => 'bearer',
    'user' => $user,
    'expires_at' => env("JWT_TTL") * 60 ], 200);
}

public function logout(){
    auth()->logout();
    return response()->json(['message' => 'Déconnexion réussie'], 200);
}

public function refresh(){
    $Token = auth()->refresh();
    return response()->json(['access_token' => $Token, 
    'token_type' => 'bearer',
    'user' => auth()->user(),
    'expires_at' => env("JWT_TTL") * 60 . 'secondes' ], 200);
}
}