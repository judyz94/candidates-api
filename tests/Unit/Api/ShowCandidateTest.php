<?php

namespace Api;

use App\Models\Candidate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShowCandidateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private const ROUTE = '/lead/';

    public function testShowReturnsCandidateWithValidTokenSuccessfully(): void
    {
        $user = User::factory()->create(['role' => 'manager']);
        $candidate = Candidate::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->getJson(self::ROUTE . $candidate->id, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
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
            ]);
    }

    public function testShowReturnsUnauthorizedWithInvalidPermissions(): void
    {
        $user = User::factory()->create(['role' => 'agent']);
        $candidate = Candidate::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->getJson(self::ROUTE . $candidate->id, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'meta' => [
                    'success' => false,
                    'errors' => ['Unauthorized']
                ]
            ]);
    }

    public function testShowReturnsNotFoundWithInvalidCandidateId(): void
    {
        $user = User::factory()->create(['role' => 'manager']);
        $invalidCandidateId = 999;
        $token = JWTAuth::fromUser($user);

        $response = $this->getJson(self::ROUTE . $invalidCandidateId, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response
            ->assertStatus(404)
            ->assertJson([
                'meta' => [
                    'success' => false,
                    'errors' => ['No lead found']
                ]
            ]);
    }
}
