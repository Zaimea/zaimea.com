<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_the_calendar_route_for_group_belongs_to()
    {
        $this->actingAs($user = User::factory()->withPersonalGroup()->create());

        $response = $this->get('calendar/'. $user->currentGroup->slug);
        $response->assertStatus(200);
    }

    public function test_user_cannot_get_the_calendar_route_for_other_group()
    {
        $this->actingAs(User::factory()->withPersonalGroup()->create());
        $otherUser = User::factory()->withPersonalGroup()->create();

        $response = $this->get('calendar/'. $otherUser->currentGroup->slug);
        $response->assertStatus(403);
    }
}
