@extends('layouts.owner')

@section('title', 'Edit Kendaraan')

@push('styles')
{{-- Gaya CSS Anda tetap sama, tidak perlu diubah --}}
<style>
    /* ... (semua style CSS Anda ada di sini) ... */
    .page-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 2rem;
        color: var(--pure-white);
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .page-title-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary-yellow), var(--secondary-yellow));
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: var(--primary-black);
    }
    .form-container {
        background: var(--secondary-black);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid var(--accent-black);
        padding: 2.5rem;
    }
    .error-container {
        background: rgba(255, 107, 107, 0.1);
        border: 1px solid rgba(255, 107, 107, 0.3);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    .error-title {
        color: var(--error-color, #FF6B6B);
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .error-list {
        list-style: none;
        padding: 0;
    }
    .error-item {
        color: var(--error-color, #FF6B6B);
        padding: 0.5rem 0;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-label {
        display: block;
        color: var(--pure-white);
        font-weight: 500;
        margin-bottom: 0.8rem;
    }
    .form-input {
        width: 100%;
        padding: 1rem 1.2rem;
        background: var(--primary-black);
        border: 1px solid var(--accent-black);
        border-radius: 8px;
        color: var(--pure-white);
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    .form-input:focus {
        outline: none;
        border-color: var(--primary-yellow);
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.2);
    }
    .form-input.error {
        border-color: var(--error-color, #FF6B6B);
    }
    .form-hint {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.6);
        margin-top: 0.5rem;
    }
    .file-input-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem 1.5rem;
        background: rgba(255, 215, 0, 0.1);
        border: 2px dashed var(--accent-black);
        border-radius: 8px;
        color: var(--primary-yellow);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .file-input-label:hover {
        background: rgba(255, 215, 0, 0.15);
        border-color: var(--primary-yellow);
    }
    .file-input {
        display: none;
    }
    .file-preview {
        margin-top: 1rem;
    }
    .file-preview img {
        max-height: 200px;
        border-radius: 8px;
    }
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--accent-black);
    }
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container mt-5">
    <h2 class="page-title">
        <span class="page-title-icon">✏️</span>
        Edit Kendaraan
    </h2>

    @if ($errors->any())
        <div class="error-container">
            <div class="error-title">Terjadi Kesalahan:</div>
            <ul class="error-list">
                @foreach ($errors->all() as $error)
                    <li class="error-item">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-container">
        <form action="{{ route('vehicles.update', $vehicle->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label for="name" class="form-label">Nama Kendaraan</label>
                    <input type="text" id="name" name="name" class="form-input @error('name') error @enderror"
                           value="{{ old('name', $vehicle->name) }}" required>
                </div>
                <div class="form-group">
                    <label for="brand" class="form-label">Merek</label>
                    <input type="text" id="brand" name="brand" class="form-input @error('brand') error @enderror"
                           value="{{ old('brand', $vehicle->brand) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="type" class="form-label">Deskripsi / Tipe</label>
                    <input type="text" id="type" name="type" class="form-input @error('type') error @enderror"
                           value="{{ old('type', $vehicle->type) }}" required>
                </div>
                <div class="form-group">
                    <label for="plate_number" class="form-label">No. Polisi</label>
                    <input type="text" id="plate_number" name="plate_number" class="form-input @error('plate_number') error @enderror"
                           value="{{ old('plate_number', $vehicle->plate_number) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="seat" class="form-label">Jumlah Seat</label>
                    <input type="number" id="seat" name="seat" class="form-input @error('seat') error @enderror"
                           value="{{ old('seat', $vehicle->seat) }}" required>
                </div>
                <div class="form-group">
                    <label for="transmission" class="form-label">Transmisi</label>
                    <select id="transmission" name="transmission" class="form-input @error('transmission') error @enderror" required>
                        <option value="Manual" {{ old('transmission', $vehicle->transmission) == 'Manual' ? 'selected' : '' }}>Manual</option>
                        <option value="Automatic" {{ old('transmission', $vehicle->transmission) == 'Automatic' ? 'selected' : '' }}>Automatic</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="fuel_type" class="form-label">Jenis Bahan Bakar</label>
                    <input type="text" id="fuel_type" name="fuel_type" class="form-input @error('fuel_type') error @enderror"
                           value="{{ old('fuel_type', $vehicle->fuel_type) }}" required>
                </div>
                <div class="form-group">
                    <label for="year" class="form-label">Tahun Keluaran</label>
                    <input type="number" id="year" name="year" class="form-input @error('year') error @enderror"
                           value="{{ old('year', $vehicle->year) }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="color" class="form-label">Warna</label>
                <input type="text" id="color" name="color" class="form-input @error('color') error @enderror"
                       value="{{ old('color', $vehicle->color) }}">
            </div>

            <div class="form-group">
                <label for="price_per_day" class="form-label">Harga per Hari (Rp)</label>
                <input type="number" id="price_per_day" name="price_per_day" class="form-input @error('price_per_day') error @enderror"
                       value="{{ old('price_per_day', $vehicle->price_per_day) }}" required>
                <div class="form-hint">Masukkan angka saja tanpa titik atau koma.</div>
            </div>

            <div class="form-group">
                <label class="form-label">Foto Kendaraan</label>
                @if ($vehicle->image)
                    <div class="file-preview mb-3">
                        <p class="form-hint mb-1">Gambar saat ini:</p>
                        <img src="{{ asset('storage/' . $vehicle->image) }}" alt="Vehicle Image" style="max-height: 150px; border-radius: 8px;">
                    </div>
                @endif
                <label for="image" class="file-input-label">📁 Pilih Gambar Baru (Opsional)</label>
                <input type="file" name="image" id="image" class="file-input">
                <div class="form-hint">Kosongkan jika tidak ingin mengganti gambar.</div>
            </div>

            <div class="form-actions">
                <a href="{{ route('owner.dashboard') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection