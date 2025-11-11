<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;

class UserController extends Controller
{
    /**
     * Menampilkan dashboard untuk user beserta daftar kendaraan.
     */
    public function dashboard(Request $request)
    {
        // Ambil input pencarian dari request
        $search = $request->input('search');

        // Mulai query semua kendaraan
        $query = Vehicle::query();

        // Jika ada input pencarian, filter datanya
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('brand', 'like', '%' . $search . '%')
                  ->orWhere('type', 'like', '%' . $search . '%')
                  ->orWhere('plate_number', 'like', '%' . $search . '%');
            });
        }

        // Ambil hasil akhir datanya
        $vehicles = $query->orderBy('created_at', 'desc')->get();

        // Rating Kendaraan
       $vehicles->each(function($vehicle){
            $vehicle->avg_rating = $vehicle->bookings()->whereNotNull('rating')->avg('rating');
       });

        // Kirim data ke view dashboard-user
        return view('layouts.user.dashboard-user', compact('vehicles'));
    }
}
