@extends('dashboard-admin')

@section('content')

{{-- CSS kustom untuk form. Sebaiknya pindahkan ke file CSS utama Anda untuk kerapian. --}}
<style>
    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--pure-white);
        text-align: center;
        margin-bottom: 2rem;
    }
    .form-container {
        background: linear-gradient(135deg, rgba(42, 42, 42, 0.8), rgba(26, 26, 26, 0.8));
        backdrop-filter: blur(10px);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 2.5rem;
        max-width: 700px; /* Lebar maksimal form agar tidak terlalu lebar di layar besar */
        margin: 0 auto;    /* Posisi form di tengah halaman */
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: rgba(255, 255, 255, 0.9);
    }
    .form-control {
        width: 100%;
        padding: 0.9rem 1rem;
        background-color: var(--secondary-black);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        color: var(--pure-white);
        font-size: 1rem;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    .form-control:focus {
        outline: none;
        border-color: var(--primary-yellow);
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.2);
    }
    /* Styling untuk pesan error validasi */
    .invalid-feedback {
        color: var(--error-color);
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }
    .form-actions {
        margin-top: 2.5rem;
        display: flex;
        justify-content: flex-end; /* Posisi tombol di sebelah kanan */
        gap: 1rem;
    }
    .btn {
        text-decoration: none;
        padding: 0.8rem 1.8rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
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
    }
    .btn-secondary {
        background: var(--accent-black);
        color: var(--pure-white);
        border: 1px solid var(--border-color);
    }
    .btn-secondary:hover {
        background: var(--secondary-black);
    }
</style>

<div class="form-container">
    <h2 class="page-title">Tambah User Baru</h2>

    {{-- Menampilkan error validasi general --}}
    @if ($errors->any())
        <div class="alert alert-danger" style="background-color: rgba(255,107,107,0.1); color: var(--error-color); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <strong>Whoops!</strong> Ada beberapa masalah dengan input Anda.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">Alamat Email</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="contoh@email.com" value="{{ old('email') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ketik ulang password" required>
        </div>

        <div class="form-actions">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan User</button>
        </div>
    </form>
</div>
@endsection
