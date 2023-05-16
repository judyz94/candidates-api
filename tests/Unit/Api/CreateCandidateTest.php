<?php

namespace Api;

use App\Models\Candidate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CreateCandidateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private const ROUTE = '/lead';

    public function testCreateCandidateSuccessfullyReturns201(): void
    {
        $user = User::factory()->create(['role' => 'manager']);
        $token = JWTAuth::fromUser($user);

        $owner = User::factory()->create()->getKey();

        $response = $this->actingAs($user)->postJson(self::ROUTE, [
            'name' => 'Mi candidato',
            'source' => 'Fotocasa',
            'owner' => $owner,
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $candidate = Candidate::first();
        $response
            ->assertStatus(201)
            ->assertJson([
                'meta' => ['success' => true],
                'data' => [
                    'id' => $candidate->id,
                    'name' => 'Mi candidato',
                    'source' => 'Fotocasa',
                    'owner' => $owner,
                    'created_at' => $candidate->created_at,
                    'created_by' => $candidate->created_by,
                ]
            ]);
    }

    public function testCreateLeadWithInvalidUserRoleReturns401(): void
    {
        $user = User::factory()->create(['role' => 'agent']);
        $token = JWTAuth::fromUser($user);

        $response = $this->postJson(self::ROUTE, [
            'name' => 'Maria',
            'source' => 'Fotocasa',
            'owner' => User::factory()->create()->getKey(),
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'meta' => [
                    'success' => false,
                    'errors' => ['Unauthorized']
                ]]);
    }
}
