<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupportController extends Controller
{
    /**
     * Display all accessible support tickets.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = SupportTicket::query()
            ->with(['user', 'assignedTo', 'messages.user'])
            ->latest();

        // Tenants should only see their own tickets
        if ($user->hasAnyRole(['tenant'])) {
            $query->where('user_id', $user->id);
        }
        // Management can see tickets for their properties or assigned tickets
        elseif ($user->isManagement()) {
            // If assigned_to is set and matches current user, or ticket is unassigned
            // Property owners/managers can see all tickets in the system
            // For now, management sees all tickets (can be refined by property later)
        }

        // Search tickets
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('ticket_number', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by assigned_to
        if ($request->filled('assigned_to') && $user->isManagement()) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $tickets = $query->paginate(10)->withQueryString();

        $statsQuery = SupportTicket::query();
        if ($user->hasAnyRole(['tenant'])) {
            $statsQuery->where('user_id', $user->id);
        }

        $openTickets = (clone $statsQuery)->where('status', 'open')->count();
        $inProgressTickets = (clone $statsQuery)->where('status', 'in_progress')->count();
        $resolvedTickets = (clone $statsQuery)->where('status', 'resolved')->count();

        // Get staff for assignment dropdown
        $staff = User::whereIn('role', ['admin', 'property_manager', 'caretaker', 'agent'])->get();

        return view('support.index', compact(
            'tickets',
            'openTickets',
            'inProgressTickets',
            'resolvedTickets',
            'staff'
        ));
    }

    /**
     * Show the ticket creation form.
     */
    public function create()
    {
        return view('support.create');
    }

    /**
     * Store a new support ticket.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|in:rent_payments,maintenance,lease_agreements,complaints,security,general_inquiries,other',
            'priority' => 'required|in:low,medium,high,critical',
            'description' => 'required|string|max:10000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachments[] = $file->store(
                    'support-attachments/'.auth()->id(),
                    'local'
                );
            }
        }

        $ticket = SupportTicket::create([
            'ticket_number' => 'TKT-'.strtoupper(Str::random(8)),
            'user_id' => auth()->id(),
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'description' => $validated['description'],
            'attachments' => $attachments,
            'status' => 'open',
        ]);

        // Create the initial message as the first conversation entry
        SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['description'],
            'is_internal' => false,
            'attachment_path' => null,
        ]);

        // Notify management staff
        $staff = User::whereIn('role', ['admin', 'property_manager', 'caretaker', 'agent'])->get();
        foreach ($staff as $member) {
            AppNotification::create([
                'user_id' => $member->id,
                'title' => 'New Support Ticket',
                'message' => "New support ticket #{$ticket->ticket_number}: {$ticket->subject}",
                'type' => 'support',
                'channel' => 'in_app',
                'related_model' => 'support_tickets',
                'related_id' => $ticket->id,
            ]);
        }

        return redirect()
            ->route('support.index')
            ->with('success', 'Your support ticket has been submitted successfully.');
    }

    /**
     * Display a specific support ticket.
     */
    public function show(SupportTicket $ticket)
    {
        $user = Auth::user();

        // Tenants can only view their own tickets
        if ($user->hasAnyRole(['tenant']) && $ticket->user_id !== $user->id) {
            abort(403, 'You can only view your own support tickets.');
        }

        $ticket->load([
            'user',
            'assignedTo',
            'messages.user',
        ]);

        $staff = User::whereIn('role', ['admin', 'property_manager', 'caretaker', 'agent'])->get();

        return view('support.show', compact('ticket', 'staff'));
    }

    /**
     * Show the ticket editing form.
     */
    public function edit(SupportTicket $ticket)
    {
        $user = Auth::user();

        // Tenants can only edit their own tickets
        if ($user->hasAnyRole(['tenant']) && $ticket->user_id !== $user->id) {
            abort(403, 'You can only edit your own support tickets.');
        }

        return view('support.edit', compact('ticket'));
    }

    /**
     * Update a support ticket.
     */
    public function update(Request $request, SupportTicket $ticket)
    {
        $user = Auth::user();

        // Tenants can only update their own tickets (and not change status to closed)
        if ($user->hasAnyRole(['tenant']) && $ticket->user_id !== $user->id) {
            abort(403, 'You can only update your own support tickets.');
        }

        $validationRules = [
            'subject' => 'required|string|max:255',
            'category' => 'required|in:rent_payments,maintenance,lease_agreements,complaints,security,general_inquiries,other',
            'priority' => 'required|in:low,medium,high,critical',
            'description' => 'required|string|max:10000',
        ];

        // Only management can change status and assign
        if ($user->isManagement()) {
            $validationRules['status'] = 'required|in:open,assigned,in_progress,awaiting_response,resolved,closed';
            $validationRules['assigned_to'] = 'nullable|exists:users,id';
        }

        $validated = $request->validate($validationRules);

        $ticket->update($validated);

        return redirect()
            ->route('support.show', $ticket)
            ->with('success', 'Support ticket updated successfully.');
    }

    /**
     * Store a reply on a support ticket.
     */
    public function storeReply(Request $request, SupportTicket $ticket)
    {
        $user = Auth::user();

        // Check authorization
        if ($user->hasAnyRole(['tenant']) && $ticket->user_id !== $user->id) {
            abort(403, 'You can only reply to your own support tickets.');
        }

        $validationRules = [
            'message' => 'required|string|max:5000',
            'is_internal' => 'boolean',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ];

        // Only management can add internal notes
        if (! $user->isManagement()) {
            unset($validationRules['is_internal']);
        }

        $validated = $request->validate($validationRules);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store(
                'support-replies/'.$ticket->id,
                'local'
            );
        }

        $ticket->messages()->create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $validated['message'],
            'is_internal' => $validated['is_internal'] ?? false,
            'attachment_path' => $attachmentPath,
        ]);

        // Update ticket status based on who replied
        if ($user->isManagement() && $ticket->status === 'open') {
            $ticket->update(['status' => 'pending']);
        } elseif ($user->hasAnyRole(['tenant']) && in_array($ticket->status, ['awaiting_response', 'resolved'])) {
            $ticket->update(['status' => 'open']);
        }

        // Notify the other party
        if ($user->hasAnyRole(['tenant'])) {
            // Notify assigned staff or all management
            $recipients = $ticket->assignedTo
                ? [$ticket->assignedTo]
                : User::whereIn('role', ['admin', 'property_manager', 'caretaker', 'agent'])->get();
            foreach ($recipients as $recipient) {
                AppNotification::create([
                    'user_id' => $recipient->id,
                    'title' => 'New Support Ticket Reply',
                    'message' => "New reply on ticket #{$ticket->ticket_number}: {$ticket->subject}",
                    'type' => 'support',
                    'channel' => 'in_app',
                    'related_model' => 'support_tickets',
                    'related_id' => $ticket->id,
                ]);
            }
        } elseif ($user->isManagement()) {
            // Notify tenant
            AppNotification::create([
                'user_id' => $ticket->user_id,
                'title' => 'Support Ticket Reply',
                'message' => "New reply on your ticket #{$ticket->ticket_number}: {$ticket->subject}",
                'type' => 'support',
                'channel' => 'in_app',
                'related_model' => 'support_tickets',
                'related_id' => $ticket->id,
            ]);
        }

        return redirect()
            ->route('support.show', $ticket)
            ->with('success', 'Your reply has been sent successfully.');
    }

    /**
     * Delete a support ticket.
     */
    public function destroy(SupportTicket $ticket)
    {
        $user = Auth::user();

        // Only management or ticket owner can delete
        if ($user->hasAnyRole(['tenant']) && $ticket->user_id !== $user->id) {
            abort(403, 'You can only delete your own support tickets.');
        }

        // Delete stored attachments
        foreach ($ticket->attachments ?? [] as $attachment) {
            Storage::disk('local')->delete($attachment);
        }

        // Delete message attachments
        foreach ($ticket->messages as $message) {
            if ($message->attachment_path) {
                Storage::disk('local')->delete($message->attachment_path);
            }
        }

        $ticket->delete();

        return redirect()
            ->route('support.index')
            ->with('success', 'Support ticket deleted successfully.');
    }
}
