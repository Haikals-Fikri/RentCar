@extends('layouts.user.user')

@section('title', 'Form Booking')

@push('styles')
    @vite('resources/css/user/Booking-form.css')
@endpush

@section('content')
<div class="form-card">
    <div class="form-header">
        <h2>Pemesanan {{ $vehicle->name }}</h2>
        <p>Lengkapi data diri Anda untuk melanjutkan pemesanan.</p>
    </div>

    {{-- Notifikasi Error Global --}}
    @if ($errors->any())
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <strong>Gagal!</strong> Mohon periksa kembali data yang Anda masukkan.
        </div>
    @endif

    <form action="{{ route('booking.store', $vehicle->id) }}" method="POST" enctype="multipart/form-data" id="bookingForm">
        @csrf
        {{-- Input Nama, Alamat, Telepon (Tidak ada perubahan) --}}
        <div class="form-group">
            <label class="form-label" for="name">Nama Lengkap</label>
            <input type="text" id="name" name="name" class="form-input @error('name') input-error @enderror" value="{{ old('name', auth()->user()->name) }}" required>
            @error('name')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="address">Alamat</label>
            <textarea id="address" name="address" class="form-textarea @error('address') input-error @enderror" required>{{ old('address') }}</textarea>
            @error('address')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="phone">No Telepon</label>
            <input type="numeric" id="phone" name="phone" class="form-input @error('phone') input-error @enderror" value="{{ old('phone') }}" required>
            @error('phone')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="price">Harga Sewa /Hari</label>
            <input type="text" class="form-input" id="price" value="Rp {{ number_format($vehicle->price_per_day, 0, ',','.') }}" readonly style="background-color: var(--primary-bg);">
        </div>

        {{-- [PERBAIKAN] Tambahkan ikon untuk custom style tanggal --}}
        <div class="form-group">
            <label class="form-label" for="start_date">Tanggal Mulai Sewa</label>
            <input type="date" name="start_date" id="start_date" class="form-input @error('start_date') input-error @enderror" value="{{ old('start_date') }}" required>
            <i class="fas fa-calendar-alt"></i>
            @error('start_date')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- [PERBAIKAN] Tambahkan ikon untuk custom style tanggal --}}
        <div class="form-group">
            <label class="form-label" for="end_date">Tanggal Selesai</label>
            <input type="date" id="end_date" name="end_date" class="form-input @error('end_date') input-error @enderror" value="{{ old('end_date') }}" required>
            <i class="fas fa-calendar-alt"></i>
            @error('end_date')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- [PERBAIKAN] Bungkus dengan wrapper untuk custom style upload file --}}
        <div class="form-group">
            <label class="form-label" for="sim_image">Upload Foto SIM</label>
            <div class="file-input-wrapper">
                <span id="file-chosen">Pilih file...</span>
                <input type="file" id="sim_image" name="sim_image" class="file-input" accept="image/*" required>
            </div>
            @error('sim_image')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- [PERBAIKAN] Bungkus dengan wrapper untuk custom style dropdown --}}
        <div class="form-group">
            <label class="form-label" for="payment_method">Metode Pembayaran</label>
            <div class="select-wrapper">
                <select id="payment_method" name="payment_method" class="form-select @error('payment_method') input-error @enderror" required>
                    <option value="" disabled selected>Pilih metode pembayaran</option>
                    <option value="Transfer Bank" {{ old('payment_method') == 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                    <option value="Bayar di Tempat" {{ old('payment_method') == 'Bayar di Tempat' ? 'selected' : '' }}>Bayar di Tempat</option>
                </select>
            </div>
            @error('payment_method')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-actions">
            <a href="{{ route('user.dashboard') }}" class="back-btn">Kembali</a>
            <button type="submit" class="submit-btn" id="submitBtn">Kirim Pesanan</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    @vite('resources/js/user/bookingform.js')
@endpush
