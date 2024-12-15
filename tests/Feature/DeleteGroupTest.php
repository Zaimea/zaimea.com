<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Zaimea\Livewire\Panel\Group\DeleteGroupForm;
use Tests\TestCase;

class DeleteGroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_groups_can_be_deleted(): void
    {
        $this->actingAs($user = User::factory()->withPersonalGroup()->create());

        $user->ownedGroups()->save($group = Group::factory()->make([
            'personal_group' => false,
        ]));

        $group->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'test-role']
        );

        Livewire::test(DeleteGroupForm::class, ['group' => $group->fresh()])
            ->call('deleteGroup');

        $this->assertNull($group->fresh());
        $this->assertCount(0, $otherUser->fresh()->groups);
    }

    public function test_personal_groups_cant_be_deleted(): void
    {
        $this->actingAs($user = User::factory()->withPersonalGroup()->create());

        Livewire::test(DeleteGroupForm::class, ['group' => $user->currentGroup])
            ->call('deleteGroup')
            ->assertHasErrors(['group']);

        $this->assertNotNull($user->currentGroup->fresh());
    }
}
