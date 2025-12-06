@extends('layouts.owner.owner')

@section('title', 'Edit Kendaraan')

@push('styles')
    @vite('resources/css/owner/vehicle-edit.css')
@endpush

@section('content')
@include('layouts.owner.partials.header', ['headerTitle' => 'Edit Kendaraan'])
<div class="container mt-5">

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
            <label class="form-label">Foto Kendaraan</label>

            <div id="image-preview-container" class="file-preview mb-3" style="{{ $vehicle->image ? '' : 'display: none;' }}">
                <p class="form-hint preview-hint mb-1" id="current-image-text">
                    @if($vehicle->image) Gambar saat ini: @endif
                </p>
                <img id="image-preview" src="{{ $vehicle->image ? asset('storage/' . $vehicle->image) : '' }}"
                    alt="Preview Gambar">
            </div>

            {{-- WRAPPER untuk label dan input file --}}
            <div class="file-input-wrapper">
                <label for="image" class="file-input-label">
                    📁 Pilih Gambar Baru (Opsional)
                </label>
                <input type="file" id="image" name="image" class="file-input" accept="image/*">
            </div>

    <div class="form-hint">Kosongkan jika tidak ingin mengubah/mengganti gambar</div>
</div>

            <div class="form-actions">
                <a href="{{ route('owner.dashboard') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    const previewContainer = document.getElementById('image-preview-container');
    const previewImage = document.getElementById('image-preview');
    const currentImageText = document.getElementById('current-image-text');

    if (imageInput && previewContainer && previewImage) {
        console.log('JavaScript loaded, elements found');

        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            console.log('File selected:', file);

            if (file) {
                // Tampilkan kontainer preview
                previewContainer.style.display = 'block';

                // Ubah teks
                if (currentImageText) {
                    currentImageText.textContent = 'Gambar yang dipilih:';
                }

                // Preview gambar baru
                const reader = new FileReader();
                reader.onload = function(e) {
                    console.log('Preview loaded');
                    previewImage.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                console.log('File input cleared');
                // Jika input file dikosongkan
                if (!previewImage.src || previewImage.src === '' || !previewImage.src.includes('storage/')) {
                    previewContainer.style.display = 'none';
                } else if (currentImageText) {
                    currentImageText.textContent = 'Gambar saat ini:';
                }
            }
        });
    } else {
        console.log('Elements not found:', {
            imageInput: !!imageInput,
            previewContainer: !!previewContainer,
            previewImage: !!previewImage
        });
    }
});
</script>
@endpush
@endsection
