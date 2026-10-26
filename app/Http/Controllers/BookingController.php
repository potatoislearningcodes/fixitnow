<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Technician;
use App\Notifications\BookingConfirmed;
use App\Notifications\BookingStatusChanged;
use App\Services\GeocodingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        private GeocodingService $geocoder
    ) {}

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
            // Nullable: a customer can leave this blank and let an admin
            // assign an available technician afterward (see
            // Admin\BookingAssignmentController).
            'technician_id' => 'nullable|exists:technicians,id',
            'building_type' => 'required|in:residential,institutional_business,hospitality',
            'service_group' => 'required|in:repair,maintenance',
            'service_type' => 'required|string|max:255',
            'rooms' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
            'address' => 'required|string|max:255',
            'scheduled_at' => 'required|date|after:now',
        ]);

        // One geocoding lookup per booking submission — a human action,
        // not a loop, so this stays well within Nominatim's usage policy.
        // If it fails for any reason, the booking is still created; the
        // map on the booking page just won't render.
        $coordinates = $this->geocoder->geocode($validated['address']);

        $booking = Booking::create([
            ...$validated,
            'lat' => $coordinates['lat'] ?? null,
            'lng' => $coordinates['lng'] ?? null,
            'customer_id' => auth()->id(),
            'status' => 'pending',
        ]);

        $booking->customer->notify(new BookingConfirmed($booking));

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
        // Only the technician assigned to this booking may change its status.
        $technician = auth()->user()->technicianProfile;
        abort_unless($technician && $booking->technician_id === $technician->id, 403);

        $validated = $request->validate([
            'status' => 'required|in:accepted,in_progress,completed,cancelled',
        ]);

        $booking->update($validated);

        $booking->customer->notify(new BookingStatusChanged($booking));

        return back()->with('status', 'Booking status updated.');
    }

    /**
     * Technician or admin: record that a completed job was paid, and how.
     * This is a manual record (cash in hand, a GCash reference, a bank
     * transfer slip) — not a live payment gateway checkout.
     */
    public function recordPayment(Request $request, Booking $booking)
    {
        $user = auth()->user();
        $isAssignedTechnician = $user->isTechnician()
            && $booking->technician_id === $user->technicianProfile?->id;

        abort_unless($isAssignedTechnician || $user->isAdmin(), 403);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,gcash,bank_transfer',
            'reference_number' => 'nullable|required_unless:payment_method,cash|string|max:255',
        ]);

        $booking->payment()->updateOrCreate(
            ['booking_id' => $booking->id],
            [...$validated, 'status' => 'paid']
        );

        return back()->with('status', 'Payment recorded.');
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