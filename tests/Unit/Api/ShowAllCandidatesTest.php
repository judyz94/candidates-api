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
    private const FORMAT = 'Y-m-d H:i:s';

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
                'data' => [$this->getData($candidate1), $this->getData($candidate2), $this->getData($candidate3)]
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
                'data' => [$this->getData($candidate1), $this->getData($candidate2)]
        ]);
    }

    private function getData($candidate): array
    {
        return [
                'id' => $candidate->id,
                'name' => $candidate->name,
                'source' => $candidate->source,
                'owner' => $candidate->owner,
                'created_at' => $candidate->created_at->format(self::FORMAT),
                'created_by' => $candidate->created_by,
        ];
    }
}
