<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Zaimea\Livewire\Panel\Group\GroupMemberManager;
use Tests\TestCase;

class LeaveGroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_leave_groups(): void
    {
        $user = User::factory()->withPersonalGroup()->create();

        $user->currentGroup->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        $this->actingAs($otherUser);

        Livewire::test(GroupMemberManager::class, ['group' => $user->currentGroup])
            ->call('leaveGroup');

        $this->assertCount(0, $user->currentGroup->fresh()->users);
    }

    public function test_group_owners_cant_leave_their_own_group(): void
    {
        $this->actingAs($user = User::factory()->withPersonalGroup()->create());

        Livewire::test(GroupMemberManager::class, ['group' => $user->currentGroup])
            ->call('leaveGroup')
            ->assertHasErrors(['group']);

        $this->assertNotNull($user->currentGroup->fresh());
    }
}
