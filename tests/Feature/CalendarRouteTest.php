<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_the_calendar_route_for_team_belongs_to()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $response = $this->get('team/'. $user->currentTeam->id .'/calendar');
        $response->assertStatus(200);
    }

    public function test_user_cannot_get_the_calendar_route_for_other_team()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());
        $otherUser = User::factory()->withPersonalTeam()->create();

        $response = $this->get('team/'. $otherUser->currentTeam->id .'/calendar');
        $response->assertStatus(403);
    }
}
