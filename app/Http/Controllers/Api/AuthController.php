<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\LoginResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = User::where('email', $request->email)->first();
            $user->tokens()->delete();
            $abilities = $user->getAllPermissions()->pluck('name')->toArray();
            $abilities = array_map(function ($ability) {
                [$action, $object] = explode('_', $ability);
                return $object . ':' . $action;
            }, array_filter($abilities, function($item) {
                return preg_match('/^(create|delete|update|pagination|detail)_[a-z]+$/', $item);
            }));
            $abilities = array_values($abilities);

            $token = $user->createToken('token', $abilities)->plainTextToken;

            return new LoginResource(['user' => $user, 'token' => $token]);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return response()->noContent();
    }

    public function register(Request $request) {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('token')->plainTextToken;

        return new LoginResource(['user' => $user, 'token' => $token]);
    }
}
