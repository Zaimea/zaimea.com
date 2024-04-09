<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Zaimea\Actions\Zaimea\Panel\Team\Setting\TransferTeam;
use Zaimea\Actions\Zaimea\Panel\Team\Setting\ValidateTeamTransfer;
use Zaimea\Models\Team\Membership;
use Zaimea\Policies\TeamPolicy;
use Zaimea\Zaimea;
use Tests\TestCase;

class TransferTeamTest extends TestCase
{
    use RefreshDatabase;

    protected function defineEnvironment($app)
    {
        parent::defineEnvironment($app);

        Gate::policy(Team::class, TeamPolicy::class);
        Zaimea::useUserModel(User::class);
        Zaimea::useMembershipModel(Membership::class);
    }

    public function test_team_can_be_transferred()
    {
        $user = User::forceCreate([
            'name' => 'Custura Laurentiu',
            'email' => 'mail@custura.de',
            'password' => 'secret',
        ]);

        $team = $user->ownedTeams()->create([
            'name' => 'Test Team',
            'personal_team' => false,
        ]);

        $team->users()->attach($otherUser = User::forceCreate([
            'name' => 'Zaimea',
            'email' => 'zaimea@custura.de',
            'password' => 'secret',
        ]), ['role' => 'admin']);

        $action = new TransferTeam;

        $action->transfer($team->owner, $team, $otherUser);

        $this->assertEquals($otherUser->id, $team->owner->id);
    }

    public function test_team_transfer_can_be_validated()
    {
        $user = User::forceCreate([
            'name' => 'Custura Laurentiu',
            'email' => 'mail@custura.de',
            'password' => 'secret',
        ]);

        $team = $user->ownedTeams()->create([
            'name' => 'Test Team',
            'personal_team' => false,
        ]);

        $action = new ValidateTeamTransfer;

        $action->validate($team->owner, $team);

        $this->assertTrue(true);
    }

    public function test_personal_team_cant_be_transferred()
    {
        $this->expectException(ValidationException::class);

        $user = User::forceCreate([
            'name' => 'Custura Laurentiu',
            'email' => 'mail@custura.de',
            'password' => 'secret',
        ]);

        $team = $user->ownedTeams()->create([
            'name' => 'Test Team',
            'personal_team' => true,
        ]);

        $action = new ValidateTeamTransfer;

        $action->validate($team->owner, $team);
    }

    public function test_non_owner_cant_transfer_team()
    {
        $this->expectException(AuthorizationException::class);

        Zaimea::useUserModel(User::class);

        $user = User::forceCreate([
            'name' => 'Custura Laurentiu',
            'email' => 'mail@custura.de',
            'password' => 'secret',
        ]);

        $team = $user->ownedTeams()->create([
            'name' => 'Test Team',
            'personal_team' => false,
        ]);

        $action = new ValidateTeamTransfer;

        $action->validate(User::forceCreate([
            'name' => 'Zaimea',
            'email' => 'zaimea@custura.de',
            'password' => 'secret',
        ]), $team);
    }
}
