@extends('layouts.user.user')
@section('title', $booking->review ? 'Edit Ulasan' : 'Beri Ulasan')

@push('styles')
    @vite('resources/css/user/rating.css')
@endpush

@section('content')
<div class="review-container">
    <h2>{{ $booking->review ? 'Edit Ulasan' : 'Berikan Ulasan' }}</h2>
    <p>Bagaimana pengalaman Anda dengan kendaraan {{ $booking->vehicle->name ?? 'Kendaraan' }}?</p>

    <div class="vehicle-info">
        <img src="{{ $booking->vehicle->image ? asset('storage/'.$booking->vehicle->image) : '/placeholder.svg' }}"
             alt="{{ $booking->vehicle->name ?? 'Kendaraan' }}" class="vehicle-image">
        <div class="vehicle-details">
            <h3>{{ $booking->vehicle->name ?? 'Kendaraan Dihapus' }}</h3>
            <p>{{ $booking->vehicle->brand ?? '-' }} - {{ $booking->vehicle->type ?? '-' }}</p>
            <p>{{ \Carbon\Carbon::parse($booking->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($booking->end_date)->format('d M Y') }}</p>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- PERUBAHAN 1: Form action sekarang menunjuk ke satu rute --}}
    {{-- ======================================================= --}}
    <form action="{{ route('booking.submitReview', $booking->id) }}" method="POST" class="review-form">
        @csrf

        <div class="form-group">
            <label>Rating:</label>
            <div class="star-rating">
                @php
                    // Ambil rating, utamakan data 'old' (jika validasi gagal), baru data dari database
                    $currentRating = old('rating', $booking->rating ?? 0);
                @endphp

                @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star {{ $currentRating >= $i ? 'active' : '' }}" data-value="{{ $i }}"></i>
                @endfor
            </div>

            {{-- ======================================================= --}}
            {{-- PERUBAHAN 2: Input value menggunakan old() --}}
            {{-- ======================================================= --}}
            <input type="hidden" name="rating" value="{{ $currentRating }}" class="rating-value" required>
        </div>

        <div class="form-group">
            <label>Ulasan Anda</label>

            {{-- ======================================================= --}}
            {{-- PERUBAHAN 3: Textarea menggunakan old() --}}
            {{-- ======================================================= --}}
            <textarea name="review" placeholder="Bagikan pengalaman Anda..." required>{{ old('review', $booking->review ?? '') }}</textarea>
        </div>

        <div class="form-actions">
            <a href="{{ route('user.reviews') }}" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary">{{ $booking->review ? 'Update Ulasan' : 'Kirim Ulasan' }}</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star-rating i');
    const ratingInput = document.querySelector('.rating-value');

    // JS tidak perlu diubah, karena sudah membaca value dari input
    // yang sekarang sudah diisi dengan data 'old' atau data database
    const currentRating = parseInt(ratingInput.value);

    // Initialize stars
    stars.forEach(star => {
        const value = parseInt(star.getAttribute('data-value'));

        // (Logika inisialisasi Anda sudah benar)
        if (value <= currentRating) star.classList.add('active');

        star.addEventListener('click', () => {
            ratingInput.value = value;
            stars.forEach(s => {
                const sVal = parseInt(s.getAttribute('data-value'));
                s.classList.toggle('active', sVal <= value);
            });
        });

        star.addEventListener('mouseenter', () => {
            stars.forEach(s => {
                s.style.transform = parseInt(s.getAttribute('data-value')) <= value ? 'scale(1.2)' : 'scale(1)';
            });
        });

        star.addEventListener('mouseleave', () => {
            stars.forEach(s => s.style.transform = 'scale(1)');
        });
    });

    // Form validation
    const form = document.querySelector('.review-form');
    form.addEventListener('submit', function(e) {
        const rating = parseInt(ratingInput.value);
        const review = form.querySelector('textarea').value.trim();
        if (rating === 0) { e.preventDefault(); alert('Silakan beri rating'); }
        if (!review) { e.preventDefault(); alert('Silakan tulis ulasan'); }
    });
});
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection
