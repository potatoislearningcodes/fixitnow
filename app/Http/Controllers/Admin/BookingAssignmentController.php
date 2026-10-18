<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Technician;
use Illuminate\Http\Request;

/**
 * Admin-only: see bookings the customer left unassigned, and hand each one
 * to an available, approved technician.
 */
class BookingAssignmentController extends Controller
{
    public function index()
    {
        $unassignedBookings = Booking::whereNull('technician_id')
            ->where('status', 'pending')
            ->with('customer')
            ->latest()
            ->get();

        $availableTechnicians = Technician::approved()
            ->with('user')
            ->get()
            ->filter(fn (Technician $t) => $t->isAvailable())
            ->values();

        return view('admin.bookings.unassigned', compact('unassignedBookings', 'availableTechnicians'));
    }

    public function assign(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'technician_id' => 'required|exists:technicians,id',
        ]);

        $technician = Technician::findOrFail($validated['technician_id']);

        abort_unless($technician->isApproved() && $technician->isAvailable(), 422, 'That technician is not available.');

        $booking->update(['technician_id' => $technician->id]);

        return back()->with('status', "Assigned to {$technician->user->name}.");
    }
}
