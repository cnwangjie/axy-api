<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class ApiTest extends TestCase
{
    public function testRootRoute()
    {
        $response = $this->get('/');

        $response->assertStatus(404);
    }
}
