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
    public function show(Request $request, int $candidateId): JsonResponse
    {
        $response = $this->validateAndAuthenticate($request, true, $candidateId);
        if ($response !== null) {
            return $response;
        }

        try {
            $candidate = Candidate::getById($candidateId);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'meta' => [
                    'success' => false,
                    'errors' => ['No lead found']
                ]
            ], 404);
        }

        $data = [
            'id' => $candidate->id,
            'name' => $candidate->name,
            'source' => $candidate->source,
            'owner' => $candidate->owner,
            'created_at' => $candidate->created_at->format('Y-m-d H:i:s'),
            'created_by' => $candidate->created_by,
        ];

        return $this->formatResponse($data);
    }

    public function showAll(Request $request): JsonResponse
    {
        $response = $this->validateAndAuthenticate($request, false);
        if ($response !== null) {
            return $response;
        }

        $user = $request->user();
        if (!$user->hasRole('manager')) {
            $candidates = Candidate::getCandidatesByOwner($user->id);
        } else {
            $candidates = Candidate::getAll();
        }

        $data = $candidates->map(function ($candidate) {
            return [
                'id' => $candidate->id,
                'name' => $candidate->name,
                'source' => $candidate->source,
                'owner' => $candidate->owner,
                'created_at' => $candidate->created_at->format('Y-m-d H:i:s'),
                'created_by' => $candidate->created_by,
            ];
        });

        return $this->formatResponse($data);
    }

    private function validateAndAuthenticate(Request $request, bool $isShow, ?int $candidateId = null): ?JsonResponse
    {
        $user = $request->user();
        $errors = [];

        if ($this->isTokenExpired($request)) {
            $errors[] = 'Token expired';
        }

        if ($isShow && !$user->hasRole('manager') && !$user->isOwner($candidateId)) {
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

        return null;
    }

    private function formatResponse(mixed $data): JsonResponse
    {
        return response()->json([
            'meta' => [
                'success' => true,
                'errors' => []
            ],
            'data' => $data
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
