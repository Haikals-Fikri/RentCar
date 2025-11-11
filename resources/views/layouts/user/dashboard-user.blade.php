@extends('layouts.user')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

@if(session('success'))
    <div class="alert-success">
        <strong>✅ Berhasil!</strong> {{ session('success') }}
    </div>
@endif

<section class="welcome-section">
    <div class="welcome-content">
        <h2 class="welcome-title">Selamat Datang, {{ Auth::user()->name }}!</h2>
        <p class="welcome-text">
            Temukan berbagai pilihan kendaraan dari berbagai pemilik rental di seluruh Indonesia.
        </p>
    </div>
    <img src="{{ asset('images/fortuner.png') }}" alt="Car Illustration" class="welcome-image"
         onerror="this.src='/placeholder.svg?height=200&width=300'">
</section>

<section class="search-section">
    <h2 class="search-title">Ayo Mulai Perjalananmu!</h2>
    <p class="search-subtitle">
        Temukan beragam pilihan kendaraan dari berbagai pemilik rental di seluruh Indonesia.
    </p>

    <form class="search-form" method="GET" action="{{ route('user.dashboard') }}">
        <input type="text" name="search" class="search-input"
               placeholder="Cari mobil berdasarkan nama, merk, atau tipe..."
               value="{{ request('search') }}">
        <button type="submit" class="search-btn">
            <span>🔍</span><span>Cari</span>
        </button>
    </form>
</section>

<section class="recommendation-section">
    <div class="section-header">
        <h2 class="section-title">Rekomendasi</h2>
        <div class="section-controls">
            <a href="{{ route('user.vehicles') }}" class="view-all-btn">Lihat Semua</a>
        </div>
    </div>

    <div class="tab-container">
        <button class="tab-btn active">Lokasi</button>
        <button class="tab-btn">Terbaru</button>
        <button class="tab-btn">Terlaris</button>
    </div>

    <div class="car-grid">
        @forelse($vehicles as $vehicle)
            @php
                $owner = $vehicle->owner;
                $ownerProfile = $owner?->ownerProfile;

                $businessName = $ownerProfile?->business_name ?? $owner?->name ?? 'Pemilik';
                $phone = $ownerProfile?->phone_number;
                $whatsapp = null;

                if ($phone) {
                    $whatsapp = preg_replace('/[^0-9]/', '', $phone);
                    if (substr($whatsapp, 0, 1) === '0') {
                        $whatsapp = '62' . substr($whatsapp, 1);
                    } elseif (substr($whatsapp, 0, 2) !== '62') {
                        $whatsapp = '62' . $whatsapp;
                    }
                }

                $instagram = $ownerProfile?->instagram ?? null;
            @endphp

            <div class="car-card">
                <div class="car-image-container">
                    <span class="car-label">{{ $vehicle->status_vehicle }}</span>
                    @if($vehicle->image)
                        <img src="{{ asset('storage/' . $vehicle->image) }}" alt="{{ $vehicle->name }}" class="car-image">
                    @else
                        <img src="/placeholder.svg?height=220&width=320" alt="{{ $vehicle->name }}" class="car-image">
                    @endif
                </div>


                <div class="car-details">
                    <h3 class="car-name">{{ $vehicle->name }}</h3>

                    <div class="car-feature">
                        <span>⭐</span>
                        <span>{{ $vehicle->avg_rating ? number_format($vehicle->avg_rating, 1) : 'Belum Ada' }}</span>
                    </div>

                    <div class="car-features">
                        <div class="car-feature">
                            <span>🏷️</span>
                            <span>{{ $vehicle->brand ?? '-' }}</span>

                        </div>
                        <div class="car-feature">
                            <span>🚗</span>
                            <span>{{ $vehicle->type ?? '-' }}</span>
                        </div>
                        <div class="car-feature">
                            <span>📅</span>
                            <span>{{ $vehicle->year ?? '-' }}</span>
                        </div>
                        <div class="car-feature">
                            <span>💺</span>
                            <span>{{ $vehicle->seat ?? '0' }} Kursi</span>
                        </div>
                        <div class="car-feature">
                            <span>🔢</span>
                            <span>{{ $vehicle->plate_number?? '-' }}</span>
                        </div>
                        <div class="car-feature">
                            <span>⚙️</span>
                            <span>{{ $vehicle->transmission ?? '-' }}</span>
                        </div>
                        <div class="car-feature">
                            <span>⛽</span>
                            <span>{{ $vehicle->fuel_type ?? '-' }}</span>
                        </div>
                        <div class="car-feature">
                            <span>📍</span>
                            <span>{{ $ownerProfile->address ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="car-price-status">
                        <div class="price-info">
                            <div class="price-amount">Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }}</div>
                            <div class="price-period">/ hari</div>
                        </div>

                        @if ($vehicle->status_vehicle == 'Tersedia')
                            <div class="car-status available">
                                <i class="fas fa-circle"></i>
                                <span>Tersedia</span>
                            </div>
                        @else
                            <div class="car-status unavailable">
                                <i class="fas fa-circle"></i>
                                <span>Tidak Tersedia</span>
                            </div>
                        @endif
                    </div>

                    @if ($vehicle->status_vehicle == 'Tersedia')
                        <a href="{{ route('booking.form', $vehicle->id) }}" class="rent-btn">
                            🚗 Pesan Sekarang
                        </a>
                    @else
                        <button class="rent-btn disabled" disabled>😞 Sudah Dipesan</button>
                    @endif

                    <div class="owner-contact-info">
                        @if ($ownerProfile)
                            <a href="{{ route('profile.owner.view', $owner->id) }}" class="business-btn">
                                <i class="fas fa-store"></i> {{ $businessName }}
                            </a>
                        @else
                            <button class="business-btn disabled" disabled>
                                <i class="fas fa-store"></i> {{ $businessName }}
                            </button>
                        @endif

                        <div class="owner-links">
                            @if ($whatsapp)
                                <a href="https://wa.me/{{ $whatsapp }}" target="_blank" class="owner-link whatsapp">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </a>
                            @endif
                            @if($instagram)
                                <a href="{{ $instagram }}" target="_blank" class="owner-link instagram">
                                    <i class="fab fa-instagram"></i> Instagram
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="no-vehicle">
                <h3>Belum Ada Kendaraan Tersedia</h3>
                <p>Saat ini belum ada kendaraan yang tersedia untuk disewa.</p>
            </div>
        @endforelse
    </div>
</section>

@endsection
