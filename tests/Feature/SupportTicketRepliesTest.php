<?php

namespace Tests\Feature;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportTicketRepliesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_a_reply_to_a_support_ticket(): void
    {
        $user = User::factory()->create();

        $ticket = SupportTicket::create([
            'ticket_number' => 'TKT-12345678',
            'user_id' => $user->id,
            'subject' => 'Payment issue',
            'category_id' => null,
            'description' => 'I need help with my last payment.',
            'priority' => 'high',
            'status' => 'open',
        ]);

        $this->actingAs($user)
            ->post(route('support.replies.store', $ticket), [
                'message' => 'Thanks for the update, we are checking it now.',
            ])
            ->assertRedirect(route('support.show', $ticket))
            ->assertSessionHas('success', 'Your reply has been sent successfully.');

        $this->assertDatabaseHas('support_ticket_messages', [
            'support_ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => 'Thanks for the update, we are checking it now.',
        ]);
    }
}
