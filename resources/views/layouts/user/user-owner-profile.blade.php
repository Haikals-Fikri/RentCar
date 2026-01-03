@extends('layouts.user.user')

@section('title', 'Profil Owner - ' . ($owner->ownerProfile->business_name ?? $owner->name))

@section('content')
@vite('resources/css/user/userownerprofile.css')

<div class="owner-profile-container">
    <!-- Header Profil -->
    <div class="owner-header-section">
        <div class="owner-avatar">
            @if($owner->ownerProfile && $owner->ownerProfile->profile_picture)
                <img src="{{ asset('storage/' . $owner->ownerProfile->profile_picture) }}" alt="{{ $owner->ownerProfile->business_name ?? $owner->name }}">
            @else
                <div class="avatar-placeholder">
                    <i class="fas fa-store"></i>
                </div>
            @endif
        </div>

        <div class="owner-info">
            <h1 class="business-name">{{ $owner->ownerProfile->business_name ?? $owner->name }}</h1>

            @if($owner->ownerProfile && $owner->ownerProfile->address)
            <div class="owner-location">
                <i class="fas fa-map-marker-alt"></i>
                <span>{{ $owner->ownerProfile->address }}</span>
            </div>
            @endif

            @if($owner->ownerProfile && $owner->ownerProfile->description)
            <p class="owner-description">{{ $owner->ownerProfile->description }}</p>
            @endif

            <div class="owner-stats">
                <div class="stat-item">
                    <span class="stat-number">{{ $owner->vehicles->count() }}</span>
                    <span class="stat-label">Kendaraan</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $owner->created_at->format('Y') }}</span>
                    <span class="stat-label">Tahun Bergabung</span>
                </div>
            </div>
        </div>

        <div class="owner-contact-actions">
            @if($owner->ownerProfile && $owner->ownerProfile->phone_number)
                @php
                    $whatsapp = preg_replace('/[^0-9]/', '', $owner->ownerProfile->phone_number);
                    if (substr($whatsapp, 0, 1) === '0') {
                        $whatsapp = '62' . substr($whatsapp, 1);
                    } elseif (substr($whatsapp, 0, 2) !== '62') {
                        $whatsapp = '62' . $whatsapp;
                    }
                @endphp
                <a href="https://wa.me/{{ $whatsapp }}" target="_blank" class="contact-btn whatsapp-btn">
                    <i class="fab fa-whatsapp"></i>
                    WhatsApp
                </a>
            @endif

            @if($owner->email)
                <a href="mailto:{{ $owner->email }}" class="contact-btn email-btn">
                    <i class="fas fa-envelope"></i>
                    Email
                </a>
            @endif
        </div>
    </div>

    <!-- Info Kontak & Sosial Media -->
    <div class="contact-info-section">
        <h2 class="section-title">Informasi Kontak & Sosial Media</h2>
        <div class="contact-grid">
            <!-- Informasi Kontak -->
            <div class="contact-group">
                <h3 class="contact-group-title">Kontak</h3>

                @if($owner->email)
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <div class="contact-details">
                        <span class="contact-label">Email</span>
                        <a href="mailto:{{ $owner->email }}" class="contact-value">{{ $owner->email }}</a>
                    </div>
                </div>
                @endif

                @if($owner->ownerProfile && $owner->ownerProfile->phone_number)
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <div class="contact-details">
                        <span class="contact-label">Telepon</span>
                        <a href="tel:{{ $owner->ownerProfile->phone_number }}" class="contact-value">{{ $owner->ownerProfile->phone_number }}</a>
                    </div>
                </div>
                @endif

                @if($owner->ownerProfile && $owner->ownerProfile->address)
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div class="contact-details">
                        <span class="contact-label">Alamat</span>
                        <span class="contact-value">{{ $owner->ownerProfile->address }}</span>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sosial Media -->
            <div class="contact-group">
                <h3 class="contact-group-title">Sosial Media</h3>

                @if($owner->ownerProfile && $owner->ownerProfile->instagram)
                <div class="contact-item">
                    <i class="fab fa-instagram"></i>
                    <div class="contact-details">
                        <span class="contact-label">Instagram</span>
                        <a href="{{ $owner->ownerProfile->instagram }}" target="_blank" class="contact-value social-link">
                            {{ '@' . basename($owner->ownerProfile->instagram) }}
                        </a>
                    </div>
                </div>
                @endif

                @if($owner->ownerProfile && $owner->ownerProfile->facebook)
                <div class="contact-item">
                    <i class="fab fa-facebook"></i>
                    <div class="contact-details">
                        <span class="contact-label">Facebook</span>
                        <a href="{{ $owner->ownerProfile->facebook }}" target="_blank" class="contact-value social-link">
                            {{ basename($owner->ownerProfile->facebook) }}
                        </a>
                    </div>
                </div>
                @endif
                @if($owner->ownerProfile && $owner->ownerProfile->tiktok)
                <div class="contact-item">
                    <i class="fab fa-tiktok"></i>
                    <div class="contact-details">
                        <span class="contact-label">TikTok</span>
                        <a href="{{ $owner->ownerProfile->tiktok }}" target="_blank" class="contact-value social-link">
                            {{ '@' . basename($owner->ownerProfile->tiktok) }}
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Jika tidak ada sosial media -->
        @if(!$owner->ownerProfile || (!$owner->ownerProfile->instagram && !$owner->ownerProfile->facebook && !$owner->ownerProfile->twitter && !$owner->ownerProfile->tiktok && !$owner->ownerProfile->youtube && !$owner->ownerProfile->linkedin && !$owner->ownerProfile->website))
        <div class="no-social-media">
            <i class="fas fa-share-alt"></i>
            <p>Owner ini belum menambahkan media sosial</p>
        </div>
        @endif
    </div>

    <!-- Kendaraan Tersedia -->
    <div class="vehicles-section">
        <div class="section-header">
            <h2 class="section-title">Kendaraan Tersedia</h2>
            <span class="vehicle-count">{{ $owner->vehicles->count() }} Kendaraan</span>
        </div>

        @if($owner->vehicles->count() > 0)
        <div class="car-grid">
            @foreach($owner->vehicles as $vehicle)
            <div class="car-card">
                <div class="car-image-container">
                    <span class="car-label">{{ $vehicle->status_vehicle }}</span>
                    @if($vehicle->image)
                        <img src="{{ asset('storage/' . $vehicle->image) }}" alt="{{ $vehicle->name }}" class="car-image">
                    @else
                        <div class="no-image-placeholder">
                            <i class="fas fa-car"></i>
                        </div>
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
                            <span></span>
                            <span>{{ $vehicle->fuel_type ?? '-' }}</span>
                        </div>
                        <div class="car-feature">
                            <span></span>
                            <span>{{ $vehicle->transmission ?? '-' }}</span>
                        </div>
                        <div class="car-feature">
                            <span></span>
                            <span>{{ $vehicle->plate_number?? '-' }}</span>
                        </div>
                        <div class="car-feature">
                            <span>📅</span>
                            <span>{{ $vehicle->year ?? '-' }}</span>
                        </div>
                        <div class="car-feature">
                            <span>💺</span>
                            <span>{{ $vehicle->seat ?? '0' }} Kursi</span>
                        </div>
                    </div>

                    <div class="car-price-status">
                        <div class="price-info">
                            <div class="price-amount">Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }}</div>
                            <div class="price-period">/ hari</div>
                        </div>

                        <div class="car-status {{ $vehicle->status_vehicle == 'Tersedia' ? 'available' : 'unavailable' }}">
                            <i class="fas fa-circle"></i>
                            <span>{{ $vehicle->status_vehicle }}</span>
                        </div>
                    </div>

                    @if($vehicle->status_vehicle == 'Tersedia')
                        <a href="{{ route('booking.form', $vehicle->id) }}" class="rent-btn">
                             Pesan Sekarang
                        </a>
                    @else
                        <button class="rent-btn disabled" disabled>
                             Sudah Dipesan
                        </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="no-vehicles">
            <div class="no-vehicles-icon">
                <i class="fas fa-car"></i>
            </div>
            <h3>Belum Ada Kendaraan Tersedia</h3>
            <p>Owner ini belum memiliki kendaraan yang tersedia untuk disewa.</p>
        </div>
        @endif
    </div>
</div>

@endsection
