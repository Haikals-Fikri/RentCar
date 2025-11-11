@extends('layouts.user')

@section('title', 'Profil Owner - ' . ($owner->ownerProfile->business_name ?? $owner->name))

@section('content')

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
                            🚗 Pesan Sekarang
                        </a>
                    @else
                        <button class="rent-btn disabled" disabled>
                            😞 Sudah Dipesan
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

<style>
.owner-profile-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.owner-header-section {
    background: rgba(42, 42, 42, 0.8);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2rem;
    border: 1px solid var(--border-color);
    display: flex;
    gap: 2rem;
    align-items: flex-start;
    margin-bottom: 2rem;
}

.owner-avatar {
    width: 120px;
    height: 120px;
    border-radius: 20px;
    overflow: hidden;
    background: rgba(255, 215, 0, 0.1);
    border: 2px solid var(--primary-yellow);
}

.owner-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 215, 0, 0.1);
    color: var(--primary-yellow);
    font-size: 2.5rem;
}

.owner-info {
    flex: 1;
}

.business-name {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-yellow), var(--secondary-yellow));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 1rem;
}

.owner-location {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: rgba(255, 255, 255, 0.7);
    margin-bottom: 1rem;
}

.owner-description {
    color: rgba(255, 255, 255, 0.8);
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.owner-stats {
    display: flex;
    gap: 2rem;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-yellow);
}

.stat-label {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.6);
}

.owner-contact-actions {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
    min-width: 200px;
}

.contact-btn {
    padding: 0.8rem 1.2rem;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.whatsapp-btn {
    background: #25D366;
    color: white;
}

.whatsapp-btn:hover {
    background: #1ebe5d;
    transform: translateY(-2px);
}

.email-btn {
    background: var(--primary-yellow);
    color: var(--primary-black);
}

.email-btn:hover {
    background: var(--secondary-yellow);
    transform: translateY(-2px);
}

.contact-info-section {
    background: rgba(42, 42, 42, 0.8);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2rem;
    border: 1px solid var(--border-color);
    margin-bottom: 2rem;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--pure-white);
    margin-bottom: 1.5rem;
}

.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.contact-group {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.contact-group-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--primary-yellow);
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.8rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.contact-item:last-child {
    border-bottom: none;
}

.contact-item i {
    font-size: 1.2rem;
    color: var(--primary-yellow);
    width: 30px;
    text-align: center;
}

.contact-details {
    display: flex;
    flex-direction: column;
    flex: 1;
}

.contact-label {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.6);
    margin-bottom: 0.3rem;
}

.contact-value {
    font-weight: 600;
    color: var(--pure-white);
    text-decoration: none;
    transition: color 0.3s ease;
}

.contact-value:hover {
    color: var(--primary-yellow);
}

.social-link {
    word-break: break-all;
}

.no-social-media {
    text-align: center;
    padding: 2rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    border: 2px dashed rgba(255, 215, 0, 0.3);
    color: rgba(255, 255, 255, 0.6);
}

.no-social-media i {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: var(--primary-yellow);
}

.vehicles-section {
    background: rgba(42, 42, 42, 0.8);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2rem;
    border: 1px solid var(--border-color);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.vehicle-count {
    background: rgba(255, 215, 0, 0.1);
    padding: 0.5rem 1rem;
    border-radius: 10px;
    border: 1px solid var(--border-color);
    font-weight: 600;
    color: var(--primary-yellow);
}

.no-vehicles {
    text-align: center;
    padding: 3rem;
    background: rgba(26, 26, 26, 0.6);
    border-radius: 20px;
    border: 2px dashed rgba(255, 215, 0, 0.3);
}

.no-vehicles-icon {
    font-size: 4rem;
    color: var(--primary-yellow);
    margin-bottom: 1rem;
}

.no-vehicles h3 {
    color: var(--primary-yellow);
    margin-bottom: 1rem;
}

.no-vehicles p {
    color: rgba(255, 255, 255, 0.7);
}

.car-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.car-card {
    background: rgba(42, 42, 42, 0.9);
    border-radius: 15px;
    overflow: hidden;
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
}

.car-card:hover {
    transform: translateY(-5px);
    border-color: var(--primary-yellow);
}

.car-image-container {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.car-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.05);
    color: var(--primary-yellow);
    font-size: 3rem;
}

.car-label {
    position: absolute;
    top: 1rem;
    left: 1rem;
    padding: 0.5rem 1rem;
    background: var(--primary-yellow);
    color: var(--primary-black);
    font-weight: 600;
    font-size: 0.8rem;
    border-radius: 20px;
}

.car-details {
    padding: 1.5rem;
}

.car-name {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--pure-white);
    margin-bottom: 1rem;
}

.car-features {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.car-feature {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.8);
}

.car-price-status {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding: 1rem 0;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.price-info {
    display: flex;
    align-items: baseline;
    gap: 0.3rem;
}

.price-amount {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--primary-yellow);
}

.price-period {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.6);
}

.car-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.8rem;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
}

.car-status.available {
    background: rgba(78, 205, 196, 0.15);
    color: var(--success-color);
}

.car-status.unavailable {
    background: rgba(255, 107, 107, 0.15);
    color: var(--error-color);
}

.rent-btn {
    display: block;
    width: 100%;
    padding: 0.8rem;
    background: var(--primary-yellow);
    color: var(--primary-black);
    text-align: center;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.rent-btn:hover {
    background: var(--secondary-yellow);
    transform: translateY(-2px);
}

.rent-btn.disabled {
    background: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.5);
    cursor: not-allowed;
    transform: none;
}

/* Responsive Design */
@media (max-width: 768px) {
    .owner-profile-container {
        padding: 1rem;
    }

    .owner-header-section {
        flex-direction: column;
        text-align: center;
    }

    .owner-avatar {
        align-self: center;
    }

    .owner-stats {
        justify-content: center;
    }

    .owner-contact-actions {
        flex-direction: row;
        min-width: auto;
    }

    .contact-grid {
        grid-template-columns: 1fr;
    }

    .car-grid {
        grid-template-columns: 1fr;
    }

    .car-features {
        grid-template-columns: 1fr;
    }
}
</style>

@endsection
