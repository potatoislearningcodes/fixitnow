<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Lets a logged-in technician fill in their specialty/bio and upload a
 * verification document (e.g. a certificate or ID). Submitting always
 * resets verification_status back to "pending" so the admin reviews the
 * latest version, including after a rejection.
 */
class TechnicianProfileController extends Controller
{
    public function edit()
    {
        $technician = auth()->user()->technicianProfile;

        return view('technician.complete-profile', compact('technician'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'specialty' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            // Keep the document optional on update so a technician can fix
            // their specialty/bio without being forced to re-upload every time.
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $technician = auth()->user()->technicianProfile;

        if ($request->hasFile('document')) {
            // Replace the old file instead of leaving orphaned uploads behind.
            if ($technician->document_path) {
                Storage::disk('public')->delete($technician->document_path);
            }
            $validated['document_path'] = $request->file('document')->store('technician-documents', 'public');
        }

        // Any edit (especially a new document) should go back in front of
        // the admin rather than silently staying approved/rejected.
        $validated['verification_status'] = 'pending';

        $technician->update($validated);

        return back()->with('status', 'Profile submitted. An admin will review it shortly.');
    }
}
