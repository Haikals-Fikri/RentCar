@extends('layouts.owner.owner')

@section('title', 'Tambah Kendaraan Baru')

@vite('resources/css/owner/D_owner.css')

@section('content')

@include('layouts.owner.partials.header', ['headerTitle' => 'Tambah Kendaraan Baru'])

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
