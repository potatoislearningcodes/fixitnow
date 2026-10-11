<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            // Customers and technicians can self-register. Admin accounts are
            // never created through this form — see the FixItNowSeeder.
            'role' => 'required|in:customer,technician',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // A technician registering gets an empty, unverified profile right
        // away, so the admin verification queue and the "complete your
        // profile" screen both have something to work with immediately.
        if ($user->role === 'technician') {
            Technician::create([
                'user_id' => $user->id,
                'verification_status' => 'pending',
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        // Technicians land on their profile form to add specialty/bio/document
        // before they can be approved. Customers go straight to the dashboard.
        if ($user->role === 'technician') {
            return redirect()->route('technician.profile.edit')
                ->with('status', 'Welcome! Please complete your technician profile so an admin can verify your account.');
        }

        return redirect(route('dashboard', absolute: false));
    }
}
