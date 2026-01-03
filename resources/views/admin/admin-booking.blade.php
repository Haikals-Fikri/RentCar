@extends('admin.dashboard-admin')

@section('content')
    <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Data Booking</h2>

    <div style="overflow-x: auto;">
        <table style="width:100%; border-collapse: collapse; background: #1A1A1A; color: white; border-radius: 8px; overflow: hidden;">
            <thead>
                <tr style="background: #2A2A2A; text-align: left;">
                    <th style="padding: 1rem;">ID</th>
                    <th style="padding: 1rem;">User</th>
                    <th style="padding: 1rem;">Kendaraan</th>
                    <th style="padding: 1rem;">Total Bayar</th>
                    <th style="padding: 1rem;">Status</th>
                    <th style="padding: 1rem;">Aksi</th>
                </tr>
            </thead>

            {{-- [DIPERBAIKI] Tambahkan bagian ini untuk menampilkan data --}}
            <tbody>
                @forelse($bookings as $booking)
                    <tr style="border-top: 1px solid #2A2A2A;">
                        <td style="padding: 1rem;">#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td style="padding: 1rem;">{{ $booking->user->name ?? 'N/A' }}</td>
                        <td style="padding: 1rem;">{{ $booking->vehicle->name ?? 'N/A' }}</td>
                        <td style="padding: 1rem;">Rp {{ number_format($booking->total_payment, 0, ',', '.') }}</td>
                        <td style="padding: 1rem;">{{ $booking->status }}</td>
                        <td style="padding: 1rem;">
                            <a href="{{ route('booking.print', $booking->id) }}" target="_blank" style="color: #fbbf24; text-decoration: none;">
                                Cetak Invoice
                            </a>
                        </td>
                    </tr>
                @empty
                    {{-- Bagian ini akan muncul jika tidak ada data booking sama sekali --}}
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 3rem;">
                            Belum ada data booking yang masuk.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>
@endsection
