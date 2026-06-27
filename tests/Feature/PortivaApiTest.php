<?php

namespace Tests\Feature;

use Tests\TestCase;

class PortivaApiTest extends TestCase
{
    public function test_portiva_profile_creation_returns_json_validation_errors(): void
    {
        $response = $this->withSession(['user' => 1])->post('/api/portiva/profiles', []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validasi gagal.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => [
                    'name',
                    'profession',
                    'about',
                    'skills',
                    'experience',
                    'contact',
                ],
            ]);
    }
}
