<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CandidateController extends Controller
{
    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $errors = [];

        if ($this->isTokenExpired($request)) {
            $errors[] = 'Token expired';
        }

        if (!$user->hasRole('manager') && !$user->isOwner($id)) {
            $errors[] = 'Unauthorized';
        }

        if (!empty($errors)) {
            return response()->json([
                'meta' => [
                    'success' => false,
                    'errors' => $errors
                ]
            ], 401);
        }

        try {
            $candidate = Candidate::getById($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'meta' => [
                    'success' => false,
                    'errors' => ['No lead found']
                ]
            ], 404);
        }

        return response()->json([
            'meta' => [
                'success' => true,
                'errors' => []
            ],
            'data' => [
                'id' => $candidate->id,
                'name' => $candidate->name,
                'source' => $candidate->source,
                'owner' => $candidate->owner,
                'created_at' => $candidate->created_at->format('Y-m-d H:i:s'),
                'created_by' => $candidate->created_by,
            ]
        ], 200);
    }

    public function isTokenExpired(Request $request): bool
    {
        $token = $request->bearerToken();

        try {
            JWTAuth::setToken($token)->authenticate();
        } catch (TokenExpiredException $e) {
            return true;
        }

        return false;
    }
}
