@extends('admin.dashboard-admin')

@section('content')

{{-- CSS kustom untuk form. Sebaiknya pindahkan ke file CSS utama Anda untuk kerapian. --}}
    @vite('resources/css/admin/admin_user_crud.css')

<div class="form-container">
    <h2 class="page-title">Tambah Owner Baru</h2>

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

    <form method="POST" action="{{ route('admin.owners.store') }}">
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
