<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Zaimea\Livewire\User\Ticket\TicketCreate;
use Zaimea\Livewire\User\Ticket\TicketShow;
use Zaimea\Models\User\Ticket\Category;
use Zaimea\Models\User\Ticket\Priority;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_the_route_support()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                        ->get('/user/support');

        $response->assertStatus(200);
    }

    public function test_user_cannot_get_the_route_support_if_is_not_authenticated()
    {
        $response = $this->get('/user/support');
        $response->assertRedirect('/login');
    }

    public function test_user_can_create_a_support_ticket()
    {
        $this->actingAs($user = User::factory()->create());

        $category = Category::create(['category' => 'Category']);
        $priority = Priority::create(['priority' => 'Priority']);

        Livewire::test(TicketCreate::class)
            ->set(['createTicket' => [
                'subject' => 'Test Create Ticket',
                'description' => 'Create ticket test description',
                'status' => 'opened',
                'file' => null,
                'user_id' => $user->id,
                'category_id' => $category->id,
                'priority_id' => $priority->id,
            ]])
            ->call('create');

        $this->assertCount(1, $user->fresh()->tickets);
        $this->assertEquals('Test Create Ticket', $user->fresh()->tickets->first()->subject);
    }

    public function test_user_can_read_his_support_ticket()
    {
        $this->actingAs($user = User::factory()->create());

        $category = Category::create(['category' => 'Category']);
        $priority = Priority::create(['priority' => 'Priority']);

        Livewire::test(TicketCreate::class)
            ->set(['createTicket' => [
                'subject' => 'Test Read Ticket',
                'description' => 'Create ticket test description',
                'status' => 'opened',
                'file' => null,
                'user_id' => $user->id,
                'category_id' => $category->id,
                'priority_id' => $priority->id,
            ]])
            ->call('create');

        $component = Livewire::test(TicketShow::class, ['ticket' => $user->fresh()->tickets->first()])
            ->assertStatus(200)
            ->assertSee('Test Read Ticket');
    }

    public function test_user_can_send_message_in_support_ticket()
    {
        $this->actingAs($user = User::factory()->create());

        $category = Category::create(['category' => 'Category']);
        $priority = Priority::create(['priority' => 'Priority']);

        Livewire::test(TicketCreate::class)
            ->set(['createTicket' => [
                'subject' => 'Test Send Message Ticket',
                'description' => 'Test description',
                'status' => 'opened',
                'file' => null,
                'user_id' => $user->id,
                'category_id' => $category->id,
                'priority_id' => $priority->id,
            ]])
            ->call('create');

        $ticket = $user->fresh()->tickets->first();
        Livewire::test(TicketShow::class, ['ticket' => $ticket])
            ->set(['ticketMessage' => 'Message sended for test'])
            ->call('sendMessage');

        $this->assertEquals('Message sended for test', $ticket->comments()->first()->message);
    }
}
