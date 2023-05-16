<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\User;
use Illuminate\Database\Seeder;

class CandidateSeeder extends Seeder
{
    public function run(): void
    {
        $agentUser = User::factory()->create(['role' => 'agent'])->id;
        $managerUser = User::factory()->create(['role' => 'manager'])->id;

        Candidate::factory()->create([
            'name' => 'Candidato 1',
            'owner' => User::factory()->create(['username' => 'Javier', 'role' => 'agent'])->id,
            'created_by' => $agentUser,
        ]);

        Candidate::factory()->create([
            'name' => 'Candidato 2',
            'owner' => User::factory()->create(['username' => 'Juan', 'role' => 'manager'])->id,
            'created_by' => $managerUser,
        ]);

        Candidate::factory(3)->create(['owner' => $managerUser]);
        Candidate::factory(3)->create(['owner' => $agentUser]);
    }
}
