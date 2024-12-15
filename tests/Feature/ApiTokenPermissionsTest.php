<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Zaimea\Features;
use Zaimea\Livewire\User\ApiTokenManager;
use Tests\TestCase;
use Zaimea\Zaimea;

class ApiTokenPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function defineEnvironment($app)
    {
        parent::defineEnvironment($app);

        Zaimea::defaultApiTokenPermissions(['read']);

        Zaimea::role('owner', 'Owner', null, [
            'create',
            'read',
            'update',
            'delete',
        ])->description('Owner users can perform any action.');

        Zaimea::role('admin', 'Administrator', null, [
            'create',
            'read',
            'update',
            'delete',
        ])->description('Administrator users can perform any action.');

        Zaimea::role('editor', 'Editor', null, [
            'read',
            'create',
            'update',
        ])->description('Editor users have the ability to read, create, and update.');
    }

    public function test_api_token_permissions_can_be_updated(): void
    {
        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');
        }

        $this->actingAs($user = User::factory()->withPersonalGroup()->create());

        $token = $user->tokens()->create([
            'name' => 'Test Token',
            'token' => Str::random(40),
            'abilities' => ['create', 'read'],
        ]);

        Livewire::test(ApiTokenManager::class)
            ->set(['managingPermissionsFor' => $token])
            ->set(['updateApiTokenForm' => [
                'permissions' => [
                    'delete',
                    'missing-permission',
                ],
            ]])
            ->call('updateApiToken');

        $this->assertTrue($user->fresh()->tokens->first()->can('delete'));
        $this->assertFalse($user->fresh()->tokens->first()->can('read'));
        $this->assertFalse($user->fresh()->tokens->first()->can('missing-permission'));
    }
}
