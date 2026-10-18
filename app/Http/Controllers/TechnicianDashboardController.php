<?php

namespace App\Http\Controllers;

/**
 * Lets a technician see every booking assigned to them without needing to
 * know a booking's URL in advance. Pairs with BookingController@show,
 * which still handles the full detail view and payment recording.
 */
class TechnicianDashboardController extends Controller
{
    public function index()
    {
        $technician = auth()->user()->technicianProfile;

        $bookings = $technician
            ->bookings()
            ->with('customer')
            ->orderByRaw("FIELD(status, 'accepted', 'in_progress', 'pending', 'completed', 'cancelled')")
            ->orderBy('scheduled_at')
            ->get();

        return view('technician.dashboard', compact('technician', 'bookings'));
    }
}
