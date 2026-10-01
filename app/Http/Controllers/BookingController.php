<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Technician;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /** Customer: browse approved technicians before booking. */
    public function create()
    {
        $technicians = Technician::approved()->with('user')->get();

        return view('bookings.create', compact('technicians'));
    }

    /** Customer: submit a new booking request. */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'technician_id' => 'required|exists:technicians,id',
            'service_type' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'address' => 'required|string|max:255',
            'scheduled_at' => 'required|date|after:now',
        ]);

        $booking = Booking::create([
            ...$validated,
            'customer_id' => auth()->id(),
            'status' => 'pending',
        ]);

        // TODO (Integration dev): fire a BookingCreated notification/email here.

        return redirect()
            ->route('bookings.show', $booking)
            ->with('status', 'Booking request sent.');
    }

    /** Customer or technician: view one booking and its current status. */
    public function show(Booking $booking)
    {
        $this->authorizeView($booking);

        return view('bookings.show', compact('booking'));
    }

    /** Customer: list their own booking history. */
    public function index()
    {
        $bookings = auth()->user()
            ->bookingsAsCustomer()
            ->with('technician.user')
            ->latest()
            ->get();

        return view('bookings.index', compact('bookings'));
    }

    /** Technician: update the status of a booking assigned to them. */
    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:accepted,in_progress,completed,cancelled',
        ]);

        $booking->update($validated);

        // TODO (Integration dev): notify the customer by email on status change.

        return back()->with('status', 'Booking status updated.');
    }

    private function authorizeView(Booking $booking): void
    {
        $user = auth()->user();
        $isOwner = $booking->customer_id === $user->id;
        $isAssignedTechnician = $user->isTechnician()
            && $booking->technician_id === $user->technicianProfile?->id;

        abort_unless($isOwner || $isAssignedTechnician || $user->isAdmin(), 403);
    }
}
