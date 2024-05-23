<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Zaimea\Actions\Zaimea\Panel\Group\Setting\TransferGroup;
use Zaimea\Actions\Zaimea\Panel\Group\Setting\ValidateGroupTransfer;
use Zaimea\Models\Group\Membership;
use Zaimea\Policies\GroupPolicy;
use Zaimea\Zaimea;
use Tests\TestCase;

class TransferGroupTest extends TestCase
{
    use RefreshDatabase;

    protected function defineEnvironment($app)
    {
        parent::defineEnvironment($app);

        Gate::policy(Group::class, GroupPolicy::class);
        Zaimea::useUserModel(User::class);
        Zaimea::useMembershipModel(Membership::class);
    }

    public function test_group_can_be_transferred()
    {
        $user = User::forceCreate([
            'name' => 'Custura Laurentiu',
            'email' => 'mail@custura.de',
            'password' => 'secret',
        ]);

        $group = $user->ownedGroups()->create([
            'name' => 'Test Group',
            'personal_group' => false,
        ]);

        $group->users()->attach($otherUser = User::forceCreate([
            'name' => 'Zaimea',
            'email' => 'zaimea@custura.de',
            'password' => 'secret',
        ]), ['role' => 'admin']);

        $action = new TransferGroup;

        $action->transfer($group->owner, $group, $otherUser);

        $this->assertEquals($otherUser->id, $group->owner->id);
    }

    public function test_group_transfer_can_be_validated()
    {
        $user = User::forceCreate([
            'name' => 'Custura Laurentiu',
            'email' => 'mail@custura.de',
            'password' => 'secret',
        ]);

        $group = $user->ownedGroups()->create([
            'name' => 'Test Group',
            'personal_group' => false,
        ]);

        $action = new ValidateGroupTransfer;

        $action->validate($group->owner, $group);

        $this->assertTrue(true);
    }

    public function test_personal_group_cant_be_transferred()
    {
        $this->expectException(ValidationException::class);

        $user = User::forceCreate([
            'name' => 'Custura Laurentiu',
            'email' => 'mail@custura.de',
            'password' => 'secret',
        ]);

        $group = $user->ownedGroups()->create([
            'name' => 'Test Group',
            'personal_group' => true,
        ]);

        $action = new ValidateGroupTransfer;

        $action->validate($group->owner, $group);
    }

    public function test_non_owner_cant_transfer_group()
    {
        $this->expectException(AuthorizationException::class);

        Zaimea::useUserModel(User::class);

        $user = User::forceCreate([
            'name' => 'Custura Laurentiu',
            'email' => 'mail@custura.de',
            'password' => 'secret',
        ]);

        $group = $user->ownedGroups()->create([
            'name' => 'Test Group',
            'personal_group' => false,
        ]);

        $action = new ValidateGroupTransfer;

        $action->validate(User::forceCreate([
            'name' => 'Zaimea',
            'email' => 'zaimea@custura.de',
            'password' => 'secret',
        ]), $group);
    }
}
