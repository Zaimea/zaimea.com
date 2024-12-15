<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Zaimea\Livewire\Panel\Group\GroupMemberManager;
use Tests\TestCase;

class RemoveGroupMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_members_can_be_removed_from_groups(): void
    {
        $this->actingAs($user = User::factory()->withPersonalGroup()->create());

        $user->currentGroup->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        Livewire::test(GroupMemberManager::class, ['group' => $user->currentGroup])
            ->set('groupMemberIdBeingRemoved', $otherUser->id)
            ->call('removeGroupMember');

        $this->assertCount(1, $user->currentGroup->fresh()->users);
    }

    public function test_only_group_owner_can_remove_group_members(): void
    {
        $user = User::factory()->withPersonalGroup()->create();

        $user->currentGroup->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        $this->actingAs($otherUser);

        Livewire::test(GroupMemberManager::class, ['group' => $user->currentGroup])
            ->set('groupMemberIdBeingRemoved', $user->id)
            ->call('removeGroupMember')
            ->assertStatus(403);
    }
}
