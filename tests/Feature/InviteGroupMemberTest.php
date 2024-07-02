<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Zaimea\Features;
use Zaimea\Livewire\Panel\Group\GroupMemberManager;
use Zaimea\Mail\GroupInvitation;
use Tests\TestCase;

class InviteGroupMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_members_can_be_invited_to_group(): void
    {
        if (! Features::sendsGroupInvitations()) {
            $this->markTestSkipped('Group invitations not enabled.');

            return;
        }

        Mail::fake();

        $this->actingAs($user = User::factory()->withPersonalGroup()->create());

        Livewire::test(GroupMemberManager::class, ['group' => $user->currentGroup])
            ->set('addGroupMemberForm', [
                'email' => 'test@example.com',
                'role' => 'admin',
                'client_id' => null,
                'rate' => '00:00',
                'quota_percent' => 100,
                'status' => 1,
            ])->call('addGroupMember');

        Mail::assertSent(GroupInvitation::class);

        $this->assertCount(1, $user->currentGroup->fresh()->groupInvitations);
    }

    public function test_group_member_invitations_can_be_cancelled(): void
    {
        if (! Features::sendsGroupInvitations()) {
            $this->markTestSkipped('Group invitations not enabled.');

            return;
        }

        Mail::fake();

        $this->actingAs($user = User::factory()->withPersonalGroup()->create());

        // Add the group member...
        $component = Livewire::test(GroupMemberManager::class, ['group' => $user->currentGroup])
            ->set('addGroupMemberForm', [
                'email' => 'test@example.com',
                'role' => 'admin',
                'client_id' => null,
                'rate' => '00:00',
                'quota_percent' => 100,
                'status' => 1,
            ])->call('addGroupMember');

        $invitationId = $user->currentGroup->fresh()->groupInvitations->first()->id;

        // Cancel the group invitation...
        $component->call('cancelGroupInvitation', $invitationId);

        $this->assertCount(0, $user->currentGroup->fresh()->groupInvitations);
    }
}
