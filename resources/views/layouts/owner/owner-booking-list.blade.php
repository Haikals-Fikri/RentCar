@extends('layouts.owner')

@section('title', 'Daftar Booking Masuk')

@section('content')

<div class="content-header">
     <div class="header-left">
        <button class="mobile-menu-toggle" id="menuToggle">
            <span></span><span></span><span></span>
        </button>
        <h1 class="system-title">Riwayat Pemesanan</h1>
    </div>
</div>

<div class="dashboard-content">
    @if(session('success'))
        <div class="alert-container success">
            <div class="alert-icon">✓</div>
            <div class="alert-message">{{ session('success') }}</div>
        </div>
    @endif

    <div class="data-table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Kendaraan</th>
                    <th>Status</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    <tr>
                        <td>#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $booking->user->name }}</td>
                        <td>{{ $booking->vehicle->name }}</td>
                        <td>{{ $booking->status }}</td>
                        <td>
                            @if($booking->rating)
                                 <span style="color: #FFD700;"><i class="fas fa-star"></i></span>
                                 <strong>{{ $booking->rating }}</strong> / 5
                            @else
                                <span>-</span>
                            @endif
                        </td>
                        <td>
                            @if($booking->review)
                                 <span style="color: #333;"><i class="fas fa-comment"></i></span>
                                 <strong>{{ $booking->review }}</strong>
                            @else
                                <span>-</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('booking.print', $booking->id) }}" class="table-action-btn" target="_blank" title="Cetak Invoice">
                               <i class="fas fa-print"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-table">
                           <div class="empty-state">Belum ada data booking.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
