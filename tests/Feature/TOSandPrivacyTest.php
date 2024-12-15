<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class TOSandPrivacyTest extends TestCase
{
    public function test_can_get_the_route_terms()
    {
        $response = $this->get('/terms-of-service');

        $response->assertStatus(200);
        $response->assertViewIs('view::pages.terms');
    }

    public function test_can_get_the_route_privacy()
    {
        $response = $this->get('/privacy-policy');

        $response->assertStatus(200);
        $response->assertViewIs('view::pages.policy');
    }
}
