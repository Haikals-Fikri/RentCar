@extends('layouts.owner.owner')

@section('title', 'Dashboard Owner')

@section('content')

@include('layouts.owner.partials.header', ['header-title' => 'Dashboard Kendaraan kamu'])

<div class="dashboard-content">
    <div class="section-header">
        <h2 class="section-title">Daftar Kendaraan Anda</h2>
    </div>

    @if(session('success'))
        <div class="alert-container success">
            <div class="alert-icon">✓</div>
            <div class="alert-message">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-container error">
            <div class="alert-icon">✗</div>
            <div class="alert-message">{{ session('error') }}</div>
        </div>
    @endif

    <div class="data-table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Merk</th>
                    <th>Plat Nomor</th> <th>Harga/Hari</th>
                    <th>Status</th> <th>Gambar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($vehicles as $vehicle)
                    <tr>
                        <td>{{ $vehicle->name }}</td>
                        <td>{{ $vehicle->brand }}</td>
                        <td>{{ $vehicle->plate_number }}</td>
                        <td>Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }}</td>

                        <td>
                            <span class="status-badge status-{{ strtolower($vehicle->status_vehicle) }}">
                                {{ str_replace('_', ' ', $vehicle->status_vehicle) }}
                            </span>
                        </td>

                        <td class="image-cell">
                            @if($vehicle->image)
                                <img src="{{ asset('storage/'.$vehicle->image) }}" class="vehicle-image" alt="{{ $vehicle->name }}">
                            @else
                                <div class="no-image">No Image</div>
                            @endif
                        </td>
                        <td>
                            <div class="table-actions">

                                @if ($vehicle->status_vehicle !== 'Tersedia')
                                    <form action="{{ route('vehicles.updateStatus', $vehicle->id) }}" method="POST" style="display:inline;" title="Set Tersedia">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status_vehicle" value="Tersedia">
                                        <button type="submit" class="table-action-btn status-available-btn">✅</button>
                                    </form>
                                @endif

                                @if ($vehicle->status_vehicle !== 'Maintanance')
                                    <form action="{{ route('vehicles.updateStatus', $vehicle->id) }}" method="POST" style="display:inline;" title="Set Maintanance">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status_vehicle" value="Maintanance">
                                        <button type="submit" class="table-action-btn status-maintenance-btn">🔧</button>
                                    </form>
                                @endif

                                <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="table-action-btn edit-btn" title="Edit">✏️</a>
                                <form action="{{ route('vehicles.destroy', $vehicle->id) }}" method="POST" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="table-action-btn delete-btn" title="Hapus" onclick="return confirm('Yakin hapus kendaraan ini?')">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-table"> <div class="empty-state">
                                <div class="empty-icon">🚗</div>
                                <div class="empty-text">Belum ada kendaraan.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
