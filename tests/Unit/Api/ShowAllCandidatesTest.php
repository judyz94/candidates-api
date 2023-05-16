<?php

namespace Api;

use App\Models\Candidate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShowAllCandidatesTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private const ROUTE = '/leads';

    public function testShowAllReturnsCandidatesForManagerSuccessfully(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $agent = User::factory()->create(['role' => 'manager']);

        $candidate1 = Candidate::factory()->create(['owner' => $manager->id]);
        $candidate2 = Candidate::factory()->create(['owner' => $manager->id]);
        $candidate3 = Candidate::factory()->create(['owner' => $agent->id]);

        $token = JWTAuth::fromUser($manager);

        $response = $this->getJson(self::ROUTE, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'meta' => [
                    'success' => true,
                    'errors' => [],
                ],
                'data' => [
                    [
                        'id' => $candidate1->id,
                        'name' => $candidate1->name,
                        'source' => $candidate1->source,
                        'owner' => $candidate1->owner,
                        'created_at' => $candidate1->created_at->format('Y-m-d H:i:s'),
                        'created_by' => $candidate1->created_by,
                    ],
                    [
                        'id' => $candidate2->id,
                        'name' => $candidate2->name,
                        'source' => $candidate2->source,
                        'owner' => $candidate2->owner,
                        'created_at' => $candidate2->created_at->format('Y-m-d H:i:s'),
                        'created_by' => $candidate2->created_by,
                    ],
                    [
                        'id' => $candidate3->id,
                        'name' => $candidate3->name,
                        'source' => $candidate3->source,
                        'owner' => $candidate3->owner,
                        'created_at' => $candidate3->created_at->format('Y-m-d H:i:s'),
                        'created_by' => $candidate3->created_by,
                    ],
                ],
            ]);
    }

    public function testShowAllReturnsCandidatesForAgent(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $manager = User::factory()->create(['role' => 'manager']);

        $candidate1 = Candidate::factory()->create(['owner' => $agent->id]);
        $candidate2 = Candidate::factory()->create(['owner' => $agent->id]);
        Candidate::factory()->create(['owner' => $manager->id]);

        $token = JWTAuth::fromUser($agent);

        $response = $this->getJson(self::ROUTE, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'meta' => [
                    'success' => true,
                    'errors' => [],
                ],
                'data' => [
                    [
                        'id' => $candidate1->id,
                        'name' => $candidate1->name,
                        'source' => $candidate1->source,
                        'owner' => $candidate1->owner,
                        'created_at' => $candidate1->created_at->format('Y-m-d H:i:s'),
                        'created_by' => $candidate1->created_by,
                    ],
                    [
                        'id' => $candidate2->id,
                        'name' => $candidate2->name,
                        'source' => $candidate2->source,
                        'owner' => $candidate2->owner,
                        'created_at' => $candidate2->created_at->format('Y-m-d H:i:s'),
                        'created_by' => $candidate2->created_by,
                    ],
                ],
            ]);
    }
}
