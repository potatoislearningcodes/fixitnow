<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Technician;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'pendingTechnicianCount' => Technician::where('verification_status', 'pending')->count(),
            'approvedTechnicianCount' => Technician::where('verification_status', 'approved')->count(),
            'customerCount' => User::where('role', 'customer')->count(),
        ]);
    }
}
