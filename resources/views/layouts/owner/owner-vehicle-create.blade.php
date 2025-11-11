@extends('layouts.owner')

@section('title', 'Tambah Kendaraan Baru')

@push('styles')
<style>
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
    .error-title { color: #FF6B6B; font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
    .error-item { color: #FF6B6B; padding: 0.3rem 0; }
    .form-group { margin-bottom: 1.5rem; }
    .form-label { display: block; color: var(--pure-white); font-weight: 500; margin-bottom: 0.8rem; }
    .form-input {
        width: 100%; padding: 1rem 1.2rem; background: var(--primary-black);
        border: 1px solid var(--accent-black); border-radius: 8px;
        color: var(--pure-white); font-size: 1rem; transition: all 0.3s ease;
    }
    .form-input:focus { outline: none; border-color: var(--primary-yellow); box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.2); }
    .form-input.error { border-color: #FF6B6B; }
    .form-hint { font-size: 0.85rem; color: rgba(255, 255, 255, 0.6); margin-top: 0.5rem; }
    .file-input-label {
        display: flex; align-items: center; gap: 0.5rem;
        padding: 1rem 1.5rem; background: rgba(255, 215, 0, 0.1);
        border: 2px dashed var(--accent-black); border-radius: 8px;
        color: var(--primary-yellow); font-weight: 600; cursor: pointer; transition: all 0.3s ease;
    }
    .file-input-label:hover { background: rgba(255, 215, 0, 0.15); border-color: var(--primary-yellow); }
    .file-input { display: none; }
    .file-preview { display: none; margin-top: 1rem; }
    .file-preview img { max-height: 200px; border-radius: 8px; }
    .form-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--accent-black); }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    @media (max-width: 768px) { .form-row { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="page-title">
    <span class="page-title-icon">🚗</span>
    <span>Tambah Kendaraan Baru</span>
</div>

<div class="form-container">
    @if ($errors->any())
        <div class="error-container">
            <div class="error-title">
                <span>⚠️</span>
                <span>Terdapat kesalahan pada form:</span>
            </div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="error-item">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('vehicles.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label for="name" class="form-label">Nama Kendaraan</label>
                <input type="text" id="name" name="name" class="form-input @error('name') error @enderror" value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label for="brand" class="form-label">Merk</label>
                <input type="text" id="brand" name="brand" class="form-input @error('brand') error @enderror" value="{{ old('brand') }}" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="type" class="form-label">Deskripsi / Tipe</label>
                <input type="text" id="type" name="type" class="form-input @error('type') error @enderror" value="{{ old('type') }}" required>
            </div>
            <div class="form-group">
                <label for="plate_number" class="form-label">No. Polisi</label>
                <input type="text" id="plate_number" name="plate_number" class="form-input @error('plate_number') error @enderror" value="{{ old('plate_number') }}" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="seat" class="form-label">Jumlah Seat</label>
                <input type="number" id="seat" name="seat" class="form-input @error('seat') error @enderror" value="{{ old('seat') }}" required>
            </div>
            <div class="form-group">
                <label for="transmission" class="form-label">Transmisi</label>
                <select id="transmission" name="transmission" class="form-input @error('transmission') error @enderror" required>
                    <option value="" disabled selected>Pilih Transmisi</option>
                    <option value="Manual" {{ old('transmission') == 'Manual' ? 'selected' : '' }}>Manual</option>
                    <option value="Automatic" {{ old('transmission') == 'Automatic' ? 'selected' : '' }}>Automatic</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="fuel_type" class="form-label">Jenis Bahan Bakar</label>
                <input type="text" id="fuel_type" name="fuel_type" class="form-input @error('fuel_type') error @enderror" value="{{ old('fuel_type') }}" required>
            </div>
            <div class="form-group">
                <label for="year" class="form-label">Tahun Keluaran</label>
                <input type="number" id="year" name="year" class="form-input @error('year') error @enderror" value="{{ old('year') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="price_per_day" class="form-label">Harga per Hari (Rp)</label>
            <input type="number" id="price_per_day" name="price_per_day" class="form-input @error('price_per_day') error @enderror" value="{{ old('price_per_day') }}" required>
            <div class="form-hint">Masukkan angka saja tanpa titik atau koma.</div>
        </div>

        <div class="form-group">
            <label class="form-label">Gambar Kendaraan</label>
            <label for="image" class="file-input-label">
                <span>📷</span>
                <span id="fileLabelText">Pilih Gambar...</span>
            </label>
            <input type="file" id="image" name="image" class="file-input" accept="image/*">
            <div class="file-preview" id="imagePreview">
                <img id="previewImg" src="#" alt="Preview Gambar">
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('owner.dashboard') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Kendaraan</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const fileLabelText = document.getElementById('fileLabelText');

    imageInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
                fileLabelText.textContent = file.name;
            }
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
            fileLabelText.textContent = 'Pilih Gambar...';
        }
    });
});
</script>
@endpush
