@extends('dashboard-admin')

@section('content')

{{-- CSS kustom untuk halaman ini. Sebaiknya pindahkan ke file CSS utama Anda untuk kerapian. --}}
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--pure-white);
    }

    .btn {
        text-decoration: none;
        padding: 0.8rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
    }

    .btn-primary {
        background: var(--primary-yellow);
        color: var(--primary-black);
        box-shadow: 0 4px 15px rgba(255, 215, 0, 0.2);
    }

    .btn-primary:hover {
        background: var(--secondary-yellow);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 215, 0, 0.3);
    }

    .data-table-container {
        background: linear-gradient(135deg, rgba(42, 42, 42, 0.8), rgba(26, 26, 26, 0.8));
        backdrop-filter: blur(10px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 2rem;
        overflow-x: auto; /* Membuat tabel bisa di-scroll horizontal di layar kecil */
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        color: var(--pure-white);
    }

    .data-table th, .data-table td {
        padding: 1rem;
        text-align: left;
        vertical-align: middle;
    }

    .data-table thead {
        border-bottom: 2px solid var(--primary-yellow);
    }

    .data-table th {
        font-size: 0.9rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--primary-yellow);
    }

    .data-table tbody tr {
        border-bottom: 1px solid var(--border-color);
        transition: background-color 0.2s ease;
    }

    .data-table tbody tr:last-child {
        border-bottom: none;
    }

    .data-table tbody tr:hover {
        background-color: rgba(255, 215, 0, 0.05);
    }

    .action-buttons {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }

    .btn-action {
        padding: 0.5rem 0.8rem;
        font-size: 0.9rem;
        border-radius: 6px;
    }

    .btn-edit {
        background-color: rgba(68, 126, 255, 0.1);
        color: #447eff;
        border: 1px solid rgba(68, 126, 255, 0.2);
    }
    .btn-edit:hover {
        background-color: rgba(68, 126, 255, 0.2);
    }

    .btn-delete {
        background: rgba(255, 107, 107, 0.1);
        color: var(--error-color);
        border: 1px solid rgba(255, 107, 107, 0.2);
    }
    .btn-delete:hover {
        background: rgba(255, 107, 107, 0.2);
    }
</style>

<div class="page-header">
    <h2 class="page-title">Manajemen Data User</h2>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
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
            @forelse($users as $index => $user)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role }}</td> {{-- Menampilkan role user --}}
                <td>
                    <div class="action-buttons">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-action btn-edit">
                            <span>✏️</span>
                            <span>Edit</span>
                        </a>
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
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
                    Belum ada data user.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
