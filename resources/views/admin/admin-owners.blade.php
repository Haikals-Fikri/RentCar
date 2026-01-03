@extends('admin.dashboard-admin')

@section('content')

{{-- CSS kustom untuk halaman ini. Sebaiknya pindahkan ke file CSS utama Anda untuk kerapian. --}}
    @vite('resources/css/admin/admin_owners.css')

<div class="page-header">
    <h2 class="page-title">Manajemen Data Owner</h2>
    <a href="{{ route('admin.owners.create') }}" class="btn btn-primary">
        <span>+</span>
        <span>Tambah User</span>
    </a>
</div>

<div class="data-table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th> {{-- Menambahkan kolom Role --}}
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($owners as $index => $user)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role }}</td> {{-- Menampilkan role user --}}
                <td>
                    <div class="action-buttons">
                        <a href="{{ route('admin.owners.edit', $user->id) }}" class="btn btn-action btn-edit">
                            <span>✏️</span>
                            <span>Edit</span>
                        </a>
                        <form action="{{ route('admin.owners.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-action btn-delete">
                                <span>🗑️</span>
                                <span>Hapus</span>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 2rem;">
                    Belum ada data user Owner.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
