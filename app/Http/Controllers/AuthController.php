<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use HttpResponses;

    public function login(LoginUserRequest $request)
    {
        $request->validated($request->all());

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return $this->error('', 'Invalid Credentials', 401);
        }

        $user = User::where('email', $request->email)->first();

        return $this->success([
            'id' => $user->id,
            'type' => 'User',
            'attributes' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $user->createToken('API Token of ' . $user->name)->plainTextToken
        ], 'Nice!');
    }

    public function register(StoreUserRequest $request)
    {
        $request->validated($request->all());

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' =>  $request->password,
        ]);

        return $this->success([
            'id' => $user->id,
            'type' => 'User',
            'attributes' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $user->createToken('API Token of ' . $user->name)->plainTextToken
        ], 'Nice!');
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return $this->success([], 'You have been logged out');
    }
}
