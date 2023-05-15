<?php

use App\Http\Controllers\Api\AuthController;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testGenerateAccessTokenWithValidCredentialsReturnsSuccessfully(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = fake()->password),
        ]);

        $request = [
            'username' => $user->username,
            'password' => $password,
        ];

        $response = $this->postJson('api/auth', $request);

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'meta' => [
                'success',
                'errors',
            ],
            'data' => [
                'token',
                'minutes_to_expire',
            ],
        ]);
        $responseData = $response->json();
        $this->assertNotNull($responseData['data']['token']);
        $this->assertTrue(is_string($responseData['data']['token']));
    }

    public function testGenerateAccessTokenWithInvalidCredentialsReturns401(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt(fake()->password),
        ]);

        $request = [
            'username' => $user->username,
            'password' => 'wrongpassword'
        ];

        $response = $this->postJson('api/auth', $request);

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'meta' => [
                'success',
                'errors'
            ]
        ]);
        $responseData = $response->json();
        $this->assertFalse($responseData['meta']['success']);
        $this->assertNotEmpty($responseData['meta']['errors']);
    }

    public function testGenerateAccessTokenWithMissingCredentialsReturns401(): void
    {
        $request = [
            'username' => '',
            'password' => ''
        ];

        $response = $this->postJson('api/auth', $request);

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'meta' => [
                'success',
                'errors'
            ]
        ]);
        $responseData = $response->json();
        $this->assertFalse($responseData['meta']['success']);
        $this->assertNotEmpty($responseData['meta']['errors']);
    }

    public function testGenerateAccessTokenWithNonExistentUserReturns404(): void
    {
        $request = [
            'username' => 'unknown user',
            'password' => 'password'
        ];

        $response = $this->postJson('api/auth', $request);

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'meta' => [
                'success',
                'errors'
            ]
        ]);
        $responseData = $response->json();
        $this->assertFalse($responseData['meta']['success']);
        $this->assertNotEmpty($responseData['meta']['errors']);
    }

    public function testGenerateTokenWithJWTExceptionReturns500Status(): void
    {
        $request = [
            'username' => 'testuser',
            'password' => 'testpassword',
        ];

        $controller = $this->getMockBuilder(AuthController::class)
            ->onlyMethods(['validateRequest', 'validateUser'])
            ->getMock();

        $controller->method('validateRequest')->willReturn(null);
        $controller->method('validateUser')->willReturn(null);

        JWTAuth::shouldReceive('attempt')
            ->with($request)
            ->andThrow(new JWTException('Error creating token'));

        $this->app->instance(AuthController::class, $controller);

        $response = $this->postJson('api/auth', $request);

        $response->assertStatus(500);
        $response->assertJson([
            'meta' => [
                'success' => false,
                'errors' => ['Could not create token'],
            ],
        ]);
    }
}
