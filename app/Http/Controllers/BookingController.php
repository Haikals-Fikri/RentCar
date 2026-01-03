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
    /**
     * Tampilkan form booking kendaraan.
     */
    public function create($vehicleId)
    {
        // Pastikan kendaraan tersedia sebelum menampilkan form
        $vehicle = Vehicle::where('id', $vehicleId)
            ->where('status_vehicle', 'Tersedia')
            ->firstOrFail();

        return view('layouts.user.booking-form', compact('vehicle'));
    }

    /**
     * Simpan data booking dan ubah status kendaraan menjadi Tidak_tersedia.
     */
    public function store(Request $request, $vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

       $validated = $request->validate([
        'name' => 'required|string|max:255',
        'address' => 'required|string',
        'phone' => [
            'required',
            'string',
            'regex:/^(08|62|\+62)[0-9]{8,11}$/'
        ],
        'start_date' => 'required|date|after_or_equal:today',
        'end_date' => 'required|date|after_or_equal:start_date',
        'sim_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        'payment_method' => 'required|string'
    ]);


        // Double check status sebelum booking
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

        // PERBAIKAN 1: Ubah status menjadi 'Tidak_tersedia' (sesuai ENUM)
        $vehicle->update(['status_vehicle' => 'Tidak_tersedia']);

        return redirect()->route('user.bookings')
            ->with('success', 'Booking berhasil! Silakan Nikmati Perjalanan Anda.');
    }

    /**
     * Daftar booking user.
     */
    public function index()
    {
        $bookings = Booking::with('vehicle')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('layouts.user.booking-list', compact('bookings'));
    }

    /**
     * User batalkan booking.
     */
    public function cancelBooking($id)
    {
        $booking = Booking::findOrFail($id);

        // PERBAIKAN KEAMANAN: Pastikan user yang membatalkan adalah pemilik booking
        if ($booking->user_id !== Auth::id()) {
            return back()->with('error', 'Akses ditolak.');
        }

        if (!in_array($booking->status, ['Disetujui'])) {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan karena status sudah '.$booking->status);
        }

        $booking->update(['status' => 'Dibatalkan User']);

        // PERBAIKAN 2: Cek status 'Tidak_tersedia' (bukan 'Booked') sebelum diubah ke 'Tersedia'
        if ($booking->vehicle->status_vehicle === 'Tidak_tersedia') {
            $booking->vehicle()->update(['status_vehicle' => 'Tersedia']);
        }

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }

    /**
     * Form complete booking (asumsi ini form review).
     */
    public function completeForm($id)
    {
        $booking = Booking::with('vehicle')->findOrFail($id);

        // PERBAIKAN KEAMANAN: Pastikan user yang melihat form adalah pemilik booking
        if ($booking->user_id !== Auth::id()) {
            return back()->with('error', 'Akses ditolak.');
        }

        return view('booking.complete', compact('booking'));
    }

    /**
     * Tampilkan riwayat booking user yang sudah selesai atau lewat tanggal.
     */
    public function userReviews()
    {
        $reviews = Booking::with('vehicle')
            ->where('user_id', Auth::id())
            ->where(function($query) {
                $query->where('status', 'Completed')
                      ->orWhere(function($q) {
                          // Yang sudah lewat end_date tapi status masih Disetujui
                          $q->where('status', 'Disetujui')
                            ->where('end_date', '<', Carbon::now());
                      });
            })
            ->latest()
            ->get();

        return view('booking.reviews', compact('reviews'));
    }


    /**
     * Proses complete booking dan submit review.
     */
    public function submitReview(Request $request, $id)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:255',
        ]);

        $booking = Booking::findOrFail($id);

        // PERBAIKAN KEAMANAN: Tambah Otorisasi
        if ($booking->user_id !== Auth::id()) {
            return redirect()->route('user.bookings')->with('error', 'Akses ditolak.');
        }

        $booking->update([
            'status' => 'Completed',
            'rating' => $validated['rating'],
            'review' => $validated['review']
        ]);

        $booking->vehicle()->update(['status_vehicle' => 'Tersedia']); // Nilai 'Tersedia' sudah benar

        return redirect()->route('user.reviews')
            ->with('success', 'Pesanan selesai. Terima kasih atas ulasannya.');
    }

    /**
     * Set kendaraan ke maintenance (HARUSNYA BERADA DI Owner/Admin Controller).
     * Kami perbaiki nilai ENUM-nya.
     */
    public function setMaintenance($id)
    {
        // PERINGATAN: Pastikan fungsi ini hanya dapat diakses oleh Admin/Owner
        $vehicle = Vehicle::findOrFail($id);

        // PERBAIKAN 3: Gunakan 'Maintanance' (sesuai ENUM di database)
        $vehicle->update(['status_vehicle' => 'Maintanance']);

        return redirect()->back()
            ->with('success', 'Status kendaraan diubah ke Maintanance.');
    }

    /**
     * Booking list untuk admin.
     */
    public function adminBooking()
    {
        $bookings = Booking::with(['vehicle', 'user'])
            ->latest()
            ->get();

        return view('admin.admin-booking', compact('bookings'));
    }

    /**
     * Booking list untuk owner.
     */
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

    /**
     * Cetak invoice PDF.
     */
    public function printPDF($id)
    {
        $booking = Booking::with('vehicle')->findOrFail($id);

        // PERBAIKAN KEAMANAN: Pastikan user yang mencetak adalah pemilik booking
        if ($booking->user_id !== Auth::id()) {
            return back()->with('error', 'Akses ditolak.');
        }

        $pdf = Pdf::loadView('booking.invoice', compact('booking'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('Invoice-Booking-'.$booking->id.'.pdf');
    }
}
