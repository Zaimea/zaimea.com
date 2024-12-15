<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Zaimea\Livewire\Panel\Group\UpdateGroupNameForm;
use Tests\TestCase;

class UpdateGroupNameTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_names_can_be_updated(): void
    {
        $this->actingAs($user = User::factory()->withPersonalGroup()->create());

        Livewire::test(UpdateGroupNameForm::class, ['group' => $user->currentGroup])
            ->set(['state' => ['name' => 'Test Update Group', 'description' => 'Test Description']])
            ->call('updateGroupName');

        $this->assertCount(1, $user->fresh()->ownedGroups);
        $this->assertEquals('Test Update Group', $user->currentGroup->fresh()->name);
    }
}
