<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class BookingController extends Controller
{
    // Tampilkan form booking kendaraan
    public function create($vehicleId)
    {
        $vehicle = Vehicle::where('id', $vehicleId)
            ->where('status_vehicle', 'Tersedia')
            ->firstOrFail();

        return view('layouts.user.booking-form', compact('vehicle'));
    }

    // Simpan data booking
    public function store(Request $request, $vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'sim_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'payment_method' => 'required|string'
        ]);

        if ($vehicle->status_vehicle !== 'Tersedia') {
            return back()->withInput()->with('error', 'Kendaraan tidak tersedia untuk dipesan.');
        }

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        if ($startDate > $endDate) {
            return back()->withInput()->with('error', 'Tanggal selesai harus setelah tanggal mulai.');
        }

        $days = $startDate->diffInDays($endDate) + 1;
        $totalPayment = $days * $vehicle->price_per_day;

        $simPath = $request->file('sim_image')->store('sims', 'public');

        $booking = Booking::create([
            'name' => $validated['name'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'sim_image' => $simPath,
            'payment_method' => $validated['payment_method'],
            'vehicle_id' => $vehicle->id,
            'user_id' => Auth::id(),
            'status' => 'Disetujui',
            'total_payment' => $totalPayment
        ]);

        $vehicle->update(['status_vehicle' => 'Booked']);

        return redirect()->route('user.bookings')
            ->with('success', 'Booking berhasil! Silakan Nikmati Perjalanan Anda.');
    }

    // Daftar booking user
    public function index()
    {
        $bookings = Booking::with('vehicle')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('layouts.user.booking-list', compact('bookings'));
    }

    // User batalkan booking
    public function cancelBooking($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id != Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki izin untuk membatalkan pesanan ini.');
        }

        if (!in_array($booking->status, ['Disetujui'])) {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan karena status sudah '.$booking->status);
        }

        $booking->update(['status' => 'Dibatalkan User']);

        if ($booking->vehicle->status_vehicle === 'Booked') {
            $booking->vehicle()->update(['status_vehicle' => 'Tersedia']);
        }

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }

    // Form complete booking
    public function completeForm($id)
    {
        $booking = Booking::with('vehicle')->findOrFail($id);

        // ========================================================
        // PERBAIKAN 1: Tambah Otorisasi Keamanan
        // ========================================================
        if ($booking->user_id != Auth::id()) {
            return redirect()->route('user.bookings')->with('error', 'Akses ditolak.');
        }

        return view('booking.complete', compact('booking'));
    }

    //review user
    public function userReviews()
    {
        // ========================================================
        // PERBAIKAN 2: Logika Error
        // ========================================================
        // Mengambil SEMUA booking milik user yang login,
        // bukan hanya yang sudah ada review.
        $reviews = Booking::with('vehicle')
            ->where('user_id', Auth::id()) // <-- Ini filternya
            ->latest()
            ->get();

        // Pastikan nama view ini ('booking.reviews') benar
        return view('booking.reviews', compact('reviews'));
    }

    // Proses complete booking
    // ========================================================
    // PERBAIKAN 3: Nama method (submitreview -> submitReview)
    // ========================================================
    public function submitReview(Request $request, $id)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:255',
        ]);

        $booking = Booking::findOrFail($id);

        // ========================================================
        // PERBAIKAN 4: Tambah Otorisasi Keamanan
        // ========================================================
        if ($booking->user_id != Auth::id()) {
            return redirect()->route('user.bookings')->with('error', 'Akses ditolak.');
        }


        // Fungsi update() ini sudah benar,
        // bisa untuk 'create' (dari null) atau 'update' (menimpa data lama)
        $booking->update([
            'status' => 'Completed',
            'rating' => $validated['rating'],
            'review' => $validated['review']
        ]);

        $booking->vehicle()->update(['status_vehicle' => 'Tersedia']);

        return redirect()->route('user.reviews')
            ->with('success', 'Pesanan selesai. Terima kasih atas ulasannya.');
    }

    // Set kendaraan ke maintenance
    public function setMaintenance($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->update(['status_vehicle' => 'Maintenance']);

        return redirect()->back()
            ->with('success', 'Status kendaraan diubah ke Maintenance.');
    }

    // Booking list untuk admin
    public function adminBooking()
    {
        $bookings = Booking::with(['vehicle', 'user'])
            ->latest()
            ->get();

        return view('admin-booking', compact('bookings'));
    }

    // Booking list untuk owner
    public function ownerBooking()
    {
        $bookings = Booking::with(['vehicle', 'user'])
            ->whereHas('vehicle', function ($query) {
                $query->where('owner_id', Auth::id());
            })
            ->latest()
            ->get();

        return view('layouts.owner.owner-booking-list', compact('bookings'));
    }

    // Cetak invoice PDF
    public function printPDF($id)
    {
        $booking = Booking::with('vehicle')->findOrFail($id);

        $pdf = Pdf::loadView('booking.invoice', compact('booking'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('Invoice-Booking-'.$booking->id.'.pdf');
    }
}
