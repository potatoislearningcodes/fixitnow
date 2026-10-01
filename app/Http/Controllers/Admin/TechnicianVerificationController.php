<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Technician;
use Illuminate\Http\Request;

/**
 * Admin-only: review technician documents and approve or reject them.
 * Only approved technicians should appear in the customer-facing listing
 * (use Technician::approved() from the model's scope for that query).
 */
class TechnicianVerificationController extends Controller
{
    public function index()
    {
        $pending = Technician::with('user')
            ->where('verification_status', 'pending')
            ->latest()
            ->get();

        return view('admin.technicians.index', compact('pending'));
    }

    public function approve(Technician $technician)
    {
        $technician->update(['verification_status' => 'approved']);

        return back()->with('status', "{$technician->user->name} has been approved.");
    }

    public function reject(Request $request, Technician $technician)
    {
        $request->validate(['admin_notes' => 'nullable|string|max:500']);

        $technician->update([
            'verification_status' => 'rejected',
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('status', "{$technician->user->name} has been rejected.");
    }
}
