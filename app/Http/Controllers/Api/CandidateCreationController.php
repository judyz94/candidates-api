<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CandidateRequest;
use App\Models\Candidate;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CandidateCreationController extends Controller
{
    public function create(CandidateRequest $request): JsonResponse
    {
        $user = $request->user();
        $token = JWTAuth::parseToken();

        try {
            $token->check();
        } catch (TokenExpiredException $e) {
            return response()->json([
                'meta' => [
                    'success' => false,
                    'errors' => ['Token expired']
                ]
            ], 401);
        }

        if (!$user->hasRole('manager')) {
            return response()->json([
                'meta' => [
                    'success' => false,
                    'errors' => ['Unauthorized']
                ]
            ], 401);
        }

        $candidate = Candidate::create([
            'name' => $request->input('name'),
            'source' => $request->input('source'),
            'owner' => $request->input('owner'),
            'created_by' => $user->id
        ]);

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
        ], 201);
    }
}
