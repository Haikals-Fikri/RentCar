<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function create($vehicleId)
    {
        $vehicle = Vehicle::where('id', $vehicleId)
            ->where('status_vehicle', 'Tersedia')
            ->firstOrFail();

        return view('layouts.user.booking-form', compact('vehicle'));
    }

    public function store(Request $request, $vehicleId)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => ['required','string','regex:/^(08|62|\+62)[0-9]{8,11}$/'],
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'sim_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'payment_method' => 'required|string'
        ]);

        $vehicle = Vehicle::findOrFail($vehicleId);

        if ($vehicle->status_vehicle !== 'Tersedia') {
            return back()->with('error', 'Kendaraan tidak tersedia.');
        }

        $days = Carbon::parse($validated['start_date'])
            ->diffInDays(Carbon::parse($validated['end_date'])) + 1;

        $totalPayment = $days * $vehicle->price_per_day;

        $simPath = $request->file('sim_image')->store('sims', 'public');

        Booking::create([
            'name' => $validated['name'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'sim_image' => $simPath,
            'payment_method' => $validated['payment_method'],
            'vehicle_id' => $vehicle->id,
            'user_id' => Auth::id(),
            'status' => 'Menunggu Pembayaran',
            'total_payment' => $totalPayment
        ]);

        $vehicle->update(['status_vehicle' => 'Tidak_tersedia']);

        return redirect()->route('user.bookings')->with('success', 'Booking berhasil.');
    }

    public function index()
    {
        $bookings = Booking::with('vehicle')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('layouts.user.booking-list', compact('bookings'));
    }

    public function cancelBooking($id)
    {
        $booking = Booking::with('vehicle')->findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            return back()->with('error', 'Akses ditolak.');
        }

        if (!in_array($booking->status, ['Menunggu Pembayaran','Menunggu Konfirmasi'])) {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan.');
        }

        $booking->update(['status' => 'Dibatalkan User']);
        $booking->vehicle->update(['status_vehicle' => 'Tersedia']);

        return back()->with('success', 'Pesanan dibatalkan.');
    }

    public function uploadPaymentProof(Request $request, $id)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $booking = Booking::where('id', $id)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['Menunggu Pembayaran','Menunggu Konfirmasi'])
            ->firstOrFail();

        // Hapus file bukti pembayaran lama jika ada
        if ($booking->payment_proof) {
            Storage::disk('public')->delete($booking->payment_proof);
        }

        // Simpan gambar bukti pembayaran
        $imagePath = $request->file('payment_proof')->store('payment_proof', 'public');

        // Update booking
        $booking->update([
            'payment_proof' => $imagePath,
            'status' => 'Menunggu Konfirmasi'
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah.');
    }

    public function approve($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->payment_method === 'Transfer Bank' && !$booking->payment_proof) {
            return back()->with('error', 'Bukti pembayaran belum tersedia.');
        }

        $booking->update(['status' => 'Disetujui']);

        return back()->with('success', 'Booking disetujui.');
    }

    public function reject($id)
    {
        $booking = Booking::findOrFail($id);

        $booking->update(['status' => 'Dibatalkan Owner']);
        $booking->vehicle()->update(['status_vehicle' => 'Tersedia']);

        return back()->with('success', 'Booking ditolak.');
    }

    // Download Image
    public function downloadPaymentImage($id)
    {
    $booking = Booking::findOrFail($id);

    // Cek apakah file ada
    if (!$booking->payment_proof || !Storage::disk('public')->exists($booking->payment_proof)) {
        return back()->with('error', 'File tidak ditemukan.');
    }

    // Mendapatkan ekstensi file asli
    $extension = pathinfo($booking->payment_proof, PATHINFO_EXTENSION);
    $filename = 'Bukti-Bayar-' . $booking->id . '.' . $extension;

    // Melakukan download langsung
    $filePath = storage_path('app/public/' . $booking->payment_proof);
    return response()->download($filePath, $filename);
    }
    public function submitReview(Request $request, $id)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:255',
        ]);

        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            return back()->with('error', 'Akses ditolak.');
        }

        $booking->update([
            'status' => 'Completed',
            'rating' => $validated['rating'],
            'review' => $validated['review']
        ]);

        $booking->vehicle()->update(['status_vehicle' => 'Tersedia']);

        return redirect()->route('user.reviews')->with('success', 'Pesanan selesai.');
    }
    public function userReviews()
    {
        // Mengambil data booking yang sudah memiliki rating dari user yang sedang login
        $reviews = Booking::with('vehicle')
            ->where('user_id', Auth::id())
            ->whereNotNull('rating') // Pastikan hanya yang sudah ada ratingnya
            ->latest()
            ->get();

        // Mengarahkan ke view daftar review
        return view('booking.reviews', compact('reviews'));
    }

    public function ownerBooking()
    {
        $bookings = Booking::with(['vehicle','user'])
            ->whereHas('vehicle', fn ($q) => $q->where('owner_id', Auth::id()))
            ->latest()
            ->get();

        return view('layouts.owner.owner-booking-list', compact('bookings'));
    }
    public function adminBooking()
    {
    // Mengambil semua data booking beserta relasi kendaraan dan usernya
    $bookings = Booking::with(['vehicle', 'user'])
        ->latest()
        ->get();

    return view('admin.admin-booking', compact('bookings'));
    }

    public function printPDF($id)
    {
        $booking = Booking::with('vehicle.owner.ownerProfile','user')->findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            return back()->with('error', 'Akses ditolak.');
        }


        $ownerProfile = $booking->vehicle->owner->ownerProfile;

        $paymentProofBase64 = null;
        if ($booking->payment_proof) {
            $paymentProofPath = storage_path('app/public/' . $booking->payment_proof);
            if (file_exists($paymentProofPath)) {
                $paymentProofData = file_get_contents($paymentProofPath);
                $paymentProofBase64 = 'data:' . mime_content_type($paymentProofPath) . ';base64,' . base64_encode($paymentProofData);
            }
        }

        $pdf = Pdf::loadView('booking.invoice', compact('booking','booking', 'ownerProfile', 'paymentProofBase64'))
            ->setPaper('A4','portrait');

        return $pdf->download('Invoice-Booking-'.$booking->id.'.pdf');
    }
}
