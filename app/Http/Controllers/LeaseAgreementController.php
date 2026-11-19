<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\LeaseAgreement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LeaseAgreementController extends Controller
{
    /**
     * Owner uploads the lease agreement document.
     */
    public function store(Request $request, Lease $lease)
    {
        $request->validate([
            'agreement_file' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB max
        ]);

        // Only owner/agent can upload
        if (! Auth::user()->hasAnyRole(['admin', 'property_manager'])) {
            return back()->withErrors(['error' => 'Only the property owner or agent can upload the lease agreement.']);
        }

        // Check if an agreement already exists for this lease
        $agreement = LeaseAgreement::where('lease_id', $lease->id)->first();

        $file = $request->file('agreement_file');
        $fileName = 'lease_agreement_'.$lease->id.'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
        $filePath = $file->storeAs('private/lease_agreements', $fileName);

        if ($agreement) {
            // Replace existing file
            if ($agreement->file_path && Storage::exists($agreement->file_path)) {
                Storage::delete($agreement->file_path);
            }
            $agreement->update([
                'file_path' => $filePath,
                'original_filename' => $file->getClientOriginalName(),
                'owner_signed' => false,
                'tenant_signed' => false,
                'owner_signed_at' => null,
                'tenant_signed_at' => null,
            ]);
        } else {
            $agreement = LeaseAgreement::create([
                'lease_id' => $lease->id,
                'file_path' => $filePath,
                'original_filename' => $file->getClientOriginalName(),
                'owner_signed' => false,
                'tenant_signed' => false,
            ]);
        }

        return redirect()
            ->route('tenants.show', $lease->tenant_id)
            ->with('success', 'Lease agreement uploaded successfully. Waiting for signatures.');
    }

    /**
     * Sign the lease agreement (owner or tenant).
     */
    public function sign(Request $request, LeaseAgreement $agreement)
    {
        $request->validate([
            'sign_as' => 'required|in:owner,tenant',
        ]);

        $user = Auth::user();

        if ($request->sign_as === 'owner') {
            if (! $user->hasAnyRole(['admin', 'property_manager'])) {
                return back()->withErrors(['error' => 'Only the property owner or agent can sign as owner.']);
            }
            $agreement->update([
                'owner_signed' => true,
                'owner_signed_at' => now(),
                'owner_signature_ip' => $request->ip(),
            ]);
        } elseif ($request->sign_as === 'tenant') {
            if (! $user->hasAnyRole(['tenant'])) {
                return back()->withErrors(['error' => 'Only the tenant can sign as tenant.']);
            }
            $agreement->update([
                'tenant_signed' => true,
                'tenant_signed_at' => now(),
                'tenant_signature_ip' => $request->ip(),
            ]);
        }

        $status = $agreement->isFullySigned() ? 'fully signed by both parties' : 'partially signed';

        return redirect()
            ->route('tenants.show', $agreement->lease->tenant_id)
            ->with('success', "Lease agreement signed as {$request->sign_as}. {$status}.");
    }

    /**
     * Download the lease agreement document (both parties can download).
     */
    public function download(LeaseAgreement $agreement)
    {
        $user = Auth::user();

        // Verify the user is authorized to view this agreement
        $lease = $agreement->lease;
        $isOwner = $user->hasAnyRole(['admin', 'property_manager']);
        $isTenant = $user->hasAnyRole(['tenant']);

        if (! $isOwner && ! $isTenant) {
            abort(403, 'Unauthorized access to this document.');
        }

        if (! Storage::exists($agreement->file_path)) {
            abort(404, 'Agreement document not found.');
        }

        return Storage::download($agreement->file_path, $agreement->original_filename);
    }

    /**
     * Show the agreement status page.
     */
    public function show(Lease $lease)
    {
        $agreement = LeaseAgreement::where('lease_id', $lease->id)->first();

        if (! $agreement) {
            return redirect()
                ->route('tenants.show', $lease->tenant_id)
                ->with('info', 'Please upload a lease agreement first.');
        }

        return view('lease_agreements.sign', compact('agreement'));
    }
}
