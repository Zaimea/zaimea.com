<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Zaimea\Livewire\Panel\Group\GroupMemberManager;
use Tests\TestCase;

class UpdateGroupMemberRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_member_roles_can_be_updated(): void
    {
        $this->actingAs($user = User::factory()->withPersonalGroup()->create());

        $user->currentGroup->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        Livewire::test(GroupMemberManager::class, ['group' => $user->currentGroup])
            ->set('managingRoleFor', $otherUser)
            ->set('currentRole', 'editor')
            ->call('updateRole');

        $this->assertTrue($otherUser->fresh()->hasGroupRole(
            $user->currentGroup->fresh(), 'editor'
        ));
    }

    public function test_only_group_owner_can_update_group_member_roles(): void
    {
        $user = User::factory()->withPersonalGroup()->create();

        $user->currentGroup->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        $this->actingAs($otherUser);

        Livewire::test(GroupMemberManager::class, ['group' => $user->currentGroup])
            ->set('managingRoleFor', $otherUser)
            ->set('currentRole', 'editor')
            ->call('updateRole')
            ->assertStatus(403);

        $this->assertTrue($otherUser->fresh()->hasGroupRole(
            $user->currentGroup->fresh(), 'admin'
        ));
    }
}
