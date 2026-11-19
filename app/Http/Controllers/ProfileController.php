<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordChangeRequest;
use App\Http\Requests\ProfileSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile page.
     */
    public function show(Request $request): View
    {
        $user = $request->user()->load(['profile', 'notificationPreferences', 'twoFactorSettings', 'sessions', 'activityLogs']);
        $user->ensureRelatedRecords();

        // Reload after ensuring records exist
        $user->load(['profile', 'notificationPreferences', 'twoFactorSettings']);

        $profile = $user->profile;
        $notificationPreferences = $user->notificationPreferences;
        $twoFactorSettings = $user->twoFactorSettings;
        $roleSpecificData = $user->getRoleSpecificData();

        return view('profile.show', compact(
            'user',
            'profile',
            'notificationPreferences',
            'twoFactorSettings',
            'roleSpecificData'
        ));
    }

    /**
     * Display the user's profile edit form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information and extended profile data.
     */
    public function update(ProfileSettingsRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Update user fields
        $userData = $request->only(['name', 'email']);
        $user->fill($userData);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $user->save();

        // Update or create profile
        $profileData = $request->only([
            'username', 'phone', 'gender', 'date_of_birth',
            'national_id', 'physical_address', 'business_name',
            'kra_pin', 'agency_name', 'assigned_property',
            'emergency_contact_name', 'emergency_contact_phone',
            'payment_details',
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $fileName = 'profile_'.$user->id.'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $filePath = $file->storeAs('profile_pictures', $fileName, 'public');

            // Delete old profile picture if exists
            $profile = $user->profile;
            if ($profile && $profile->profile_picture_path && Storage::disk('public')->exists($profile->profile_picture_path)) {
                Storage::disk('public')->delete($profile->profile_picture_path);
            }

            $profileData['profile_picture_path'] = $filePath;
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        // Update notification preferences if provided
        $notificationFields = [
            'email_notifications', 'sms_notifications', 'in_app_notifications',
            'rent_payment_notifications', 'maintenance_notifications', 'system_announcements',
        ];

        $notificationData = $request->only($notificationFields);
        if (! empty(array_filter($notificationData))) {
            $user->notificationPreferences()->updateOrCreate(
                ['user_id' => $user->id],
                $notificationData
            );
        }

        return Redirect::route('profile.show')->with('status', 'profile-updated');
    }

    /**
     * Change the user's password.
     */
    public function changePassword(PasswordChangeRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        return back()->with('status', 'password-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Delete profile picture if exists
        if ($user->profile && $user->profile->profile_picture_path && Storage::disk('public')->exists($user->profile->profile_picture_path)) {
            Storage::disk('public')->delete($user->profile->profile_picture_path);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Toggle two-factor authentication.
     */
    public function toggleTwoFactor(Request $request): RedirectResponse
    {
        $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);

        $user = $request->user();
        $user->ensureRelatedRecords();

        $user->twoFactorSettings->update([
            'is_enabled' => $request->boolean('enabled'),
        ]);

        return back()->with('status', 'two-factor-'.($request->boolean('enabled') ? 'enabled' : 'disabled'));
    }

    /**
     * Download the user's profile picture.
     */
    public function downloadPicture(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;

        if (! $profile || ! $profile->profile_picture_path || ! Storage::disk('public')->exists($profile->profile_picture_path)) {
            abort(404, 'Profile picture not found.');
        }

        return Storage::disk('public')->download($profile->profile_picture_path);
    }

    /**
     * Delete the user's profile picture.
     */
    public function deletePicture(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->profile;

        if ($profile && $profile->profile_picture_path && Storage::disk('public')->exists($profile->profile_picture_path)) {
            Storage::disk('public')->delete($profile->profile_picture_path);
            $profile->update(['profile_picture_path' => null]);
        }

        return back()->with('status', 'profile-picture-removed');
    }
}
