<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementRecipient;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementsController extends Controller
{
    /**
     * Display announcements for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->hasAnyRole(['tenant'])) {
            $tenant = Tenant::where('email', $user->email)->first();

            if (! $tenant) {
                abort(403, 'Tenant record not found.');
            }

            $announcements = Announcement::forTenant($tenant)
                ->with('creator')
                ->orderBy('is_pinned', 'desc')
                ->orderByDesc('created_at')
                ->paginate(15);

            // Mark as read
            foreach ($announcements as $announcement) {
                AnnouncementRecipient::updateOrCreate(
                    ['announcement_id' => $announcement->id, 'tenant_id' => $tenant->id],
                    ['is_read' => true, 'read_at' => now()]
                );
            }

            return view('announcements.index', compact('announcements', 'tenant'));
        }

        // Management users see all announcements they created
        $announcements = Announcement::where('created_by', $user->id)
            ->with('creator')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('announcements.index', compact('announcements', 'user'));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create()
    {
        if (! Auth::user()->isManagement()) {
            abort(403, 'Only management can create announcements.');
        }

        $properties = Property::all();
        $units = Unit::all();
        $tenants = Tenant::all();

        return view('announcements.create', compact('properties', 'units', 'tenants'));
    }

    /**
     * Store a new announcement.
     */
    public function store(Request $request)
    {
        if (! Auth::user()->isManagement()) {
            abort(403, 'Only management can create announcements.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,maintenance,security,meeting,utility',
            'target_type' => 'required|in:all,property,unit,tenant',
            'property_id' => 'nullable|exists:properties,id',
            'unit_id' => 'nullable|exists:units,id',
            'tenant_id' => 'nullable|exists:tenants,id',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:published_at',
            'is_pinned' => 'boolean',
        ]);

        $announcement = Announcement::create(array_merge($validated, [
            'created_by' => Auth::id(),
        ]));

        // Create recipients based on target type
        if ($validated['target_type'] === 'all') {
            $tenantIds = Tenant::all()->pluck('id');
        } elseif ($validated['target_type'] === 'property') {
            $tenantIds = Tenant::whereHas('unit', function ($q) use ($validated) {
                $q->where('property_id', $validated['property_id']);
            })->pluck('id');
        } elseif ($validated['target_type'] === 'unit') {
            $tenantIds = Tenant::where('unit_id', $validated['unit_id'])->pluck('id');
        } else {
            $tenantIds = [$validated['tenant_id']];
        }

        foreach ($tenantIds as $tenantId) {
            AnnouncementRecipient::create([
                'announcement_id' => $announcement->id,
                'tenant_id' => $tenantId,
            ]);
        }

        // Create notifications for recipients
        NotificationController::notifyAnnouncement(
            $tenantIds->toArray(),
            $validated['title'],
            $validated['content']
        );

        return redirect()
            ->route('announcements.index')
            ->with('success', 'Announcement published successfully.');
    }

    /**
     * Display a specific announcement.
     */
    public function show(Announcement $announcement)
    {
        $user = Auth::user();

        if ($user->hasAnyRole(['tenant'])) {
            $tenant = Tenant::where('email', $user->email)->first();
            if (! $tenant) {
                abort(403, 'Tenant record not found.');
            }

            // Check if tenant is a recipient
            $isRecipient = AnnouncementRecipient::where('announcement_id', $announcement->id)
                ->where('tenant_id', $tenant->id)
                ->exists();

            // Also check if it's an "all" or property/unit announcement
            if (! $isRecipient) {
                abort(403, 'You do not have access to this announcement.');
            }

            // Mark as read
            AnnouncementRecipient::updateOrCreate(
                ['announcement_id' => $announcement->id, 'tenant_id' => $tenant->id],
                ['is_read' => true, 'read_at' => now()]
            );

            return view('announcements.show', compact('announcement', 'tenant'));
        }

        // Management can view announcements they created
        if ($announcement->created_by !== $user->id && ! $user->hasAnyRole(['admin'])) {
            abort(403, 'Unauthorized access.');
        }

        return view('announcements.show', compact('announcement', 'user'));
    }

    /**
     * Edit an announcement.
     */
    public function edit(Announcement $announcement)
    {
        if (! Auth::user()->isManagement()) {
            abort(403, 'Only management can edit announcements.');
        }

        if ($announcement->created_by !== Auth::id() && ! Auth::user()->hasAnyRole(['admin'])) {
            abort(403, 'You can only edit your own announcements.');
        }

        $properties = Property::all();
        $units = Unit::all();
        $tenants = Tenant::all();

        return view('announcements.edit', compact('announcement', 'properties', 'units', 'tenants'));
    }

    /**
     * Update an announcement.
     */
    public function update(Request $request, Announcement $announcement)
    {
        if (! Auth::user()->isManagement()) {
            abort(403, 'Only management can edit announcements.');
        }

        if ($announcement->created_by !== Auth::id() && ! Auth::user()->hasAnyRole(['admin'])) {
            abort(403, 'You can only edit your own announcements.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,maintenance,security,meeting,utility',
            'target_type' => 'required|in:all,property,unit,tenant',
            'property_id' => 'nullable|exists:properties,id',
            'unit_id' => 'nullable|exists:units,id',
            'tenant_id' => 'nullable|exists:tenants,id',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:published_at',
            'is_pinned' => 'boolean',
        ]);

        $announcement->update($validated);

        return redirect()
            ->route('announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    /**
     * Delete an announcement.
     */
    public function destroy(Announcement $announcement)
    {
        if (! Auth::user()->isManagement()) {
            abort(403, 'Only management can delete announcements.');
        }

        if ($announcement->created_by !== Auth::id() && ! Auth::user()->hasAnyRole(['admin'])) {
            abort(403, 'You can only delete your own announcements.');
        }

        AnnouncementRecipient::where('announcement_id', $announcement->id)->delete();
        $announcement->delete();

        return redirect()
            ->route('announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }
}
