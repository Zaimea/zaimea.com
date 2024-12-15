<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Zaimea\Livewire\Group\CreateGroupForm;
use Tests\TestCase;

class CreateGroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_groups_can_be_created(): void
    {
        $this->actingAs($user = User::factory()->withPersonalGroup()->create());

        Livewire::test(CreateGroupForm::class)
            ->set(['state' => ['name' => 'Test Group']])
            ->call('createGroup');

        $this->assertCount(2, $user->fresh()->ownedGroups);
        $this->assertEquals('Test Group', $user->fresh()->ownedGroups()->latest('id')->first()->name);
    }
}
