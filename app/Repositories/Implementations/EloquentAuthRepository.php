<?php

namespace App\Repositories\Implementations;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Actor;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EloquentAuthRepository implements AuthRepositoryInterface
{

    public function register(RegisterRequest $request)
    {
        $firstName = $request->validated('first_name');
        $lastName = $request->validated('last_name');
        $email = $request->validated('email');
        $password = $request->validated('password');

        $actor = new Actor();
        $actor->first_name = $firstName;
        $actor->last_name = $lastName;
        $actor->email = $email;
        $actor->password = Hash::make($password);

        $actor->save();
        $actor->settings()->create([
            'actor_id' => $actor->id,
            'explicit' => true,
            'history' => true
        ]);
        return response()->json(['message' => 'You have successfully registered.'], 201);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $rememberMe = $request->get('rememberMe');
        if(Auth::attempt($credentials, $rememberMe)){
            $user = Auth::user();
            if(\auth()->check()) {
                \auth()->user()->tokens()->delete();
            }
            $token = $user->createToken('token', [''])->plainTextToken;

            return response(['token' => $token]);
        }
        return response(['message' => 'Invalid credentials'], 401);
    }

    public function logout()
    {
        $userIsLoggedIn = Auth::hasUser();
        if($userIsLoggedIn) {
            Auth::logout();
        }
        return response(['message' => "Successfully logged out!"], 200);
    }
}
