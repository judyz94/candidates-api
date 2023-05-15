<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;

class AuthController extends Controller
{
    public function generateAccessToken(Request $request): JsonResponse
    {
        $credentials = $request->only(['username', 'password']);
        $validationResponse = $this->validateRequest($credentials);
        $userResponse = $this->validateUser($credentials);

        if ($validationResponse || $userResponse) {
            return $validationResponse ?? $userResponse;
        }

        return $this->generateTokenResponse($credentials);
    }

    private function generateTokenResponse(array $credentials): JsonResponse
    {
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'meta' => [
                        'success' => false,
                        'errors' => ['Password incorrect for: ' . $credentials['username']]
                    ]
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'meta' => [
                    'success' => false,
                    'errors' => ['Could not create token']
                ]
            ], 500);
        }

        return response()->json([
            'meta' => [
                'success' => true,
                'errors' => []
            ],
            'data' => [
                'token' => $token,
                'minutes_to_expire' => config('jwt.ttl')
            ]
        ], 200);
    }

    public function validateRequest(array $credentials): ?JsonResponse
    {
        $validator = Validator::make($credentials, [
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'meta' => [
                    'success' => false,
                    'errors' => $validator->errors()->toArray()
                ]
            ], 401);
        }

        return null;
    }

    public function validateUser(array $credentials): ?JsonResponse
    {
        $user = User::where('username', $credentials['username'])->first();

        if (!$user) {
            return response()->json([
                'meta' => [
                    'success' => false,
                    'errors' => ['User not found']
                ]
            ], 404);
        }

        return null;
    }
}
