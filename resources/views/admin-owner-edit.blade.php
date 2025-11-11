@extends('layouts.owner')

@section('title', 'Edit Kendaraan')

@section('content')

<div class="section-header">
    <h2 class="section-title">Edit Kendaraan: {{ $vehicle->name }}</h2>
    <a href="{{ route('owner.dashboard') }}" class="btn btn-secondary">
        <span>Kembali</span>
    </a>
</div>

<div class="form-container">
    <form action="{{ route('vehicles.update', $vehicle->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- NAMA --}}
        <div class="form-group">
            <label for="name" class="form-label">Nama Kendaraan</label>
            <input type="text" id="name" name="name" class="form-input" value="{{ $vehicle->name }}" required>
        </div>

        {{-- TIPE --}}
        <div class="form-group">
            <label for="type" class="form-label">Tipe</label>
            <input type="text" id="type" name="type" class="form-input" value="{{ $vehicle->type }}" required>
        </div>

        {{-- MERK --}}
        <div class="form-group">
            <label for="brand" class="form-label">Merk</label>
            <input type="text" id="brand" name="brand" class="form-input" value="{{ $vehicle->brand }}" required>
        </div>

        {{-- Sisa form fields Anda (No Polisi, Harga, Gambar, dll.) --}}

        <button type="submit" class="btn btn-primary">Update Kendaraan</button>
    </form>
</div>

@endsection
