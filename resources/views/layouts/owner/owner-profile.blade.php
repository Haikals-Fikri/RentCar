@extends('layouts.owner')

@section('title', 'Profil Owner')
@section('page-title', 'Profil Saya')

@section('content')
<style>
    /* CSS tetap sama seperti sebelumnya */
    body {
        background: linear-gradient(135deg, #121212 0%, #1a1a2a 100%);
        font-family: 'Poppins', sans-serif;
        color: #fff;
        margin: 0;
        padding: 0;
        min-height: 100vh;
    }

    main { padding: 0 !important; margin: 0 !important; }

    .profile-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        min-height: calc(100vh - 60px);
        padding: 2rem 1rem;
    }

    .profile-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .profile-header img {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        border: 4px solid #ffcc00;
        object-fit: cover;
        box-shadow: 0 0 20px rgba(255, 204, 0, 0.4);
        margin-bottom: 1rem;
    }

    .profile-header h2 {
        font-size: 1.8rem;
        color: #ffcc00;
        font-weight: 600;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .profile-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        width: 100%;
        max-width: 1000px;
    }

    .profile-section {
        background: linear-gradient(145deg, #242424, #2a2a2a);
        border-radius: 15px;
        padding: 2rem;
        border: 1px solid #333;
        box-shadow: 0 5px 15px rgba(0,0,0,0.4);
        transition: transform 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .profile-section:hover { transform: translateY(-3px); }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid rgba(255, 204, 0, 0.3);
        padding-bottom: .5rem;
    }

    .section-header h3 { font-size: 1.2rem; font-weight: 600; color: #ffcc00; }

    .edit-btn {
        color: #ffcc00;
        text-decoration: none;
        border: 1px solid #ffcc00;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: .85rem;
        transition: all .3s ease;
        cursor: pointer;
        background: transparent;
    }
    .edit-btn:hover { background: #ffcc00; color: #121212; }

    .info-item {
        display: flex;
        justify-content: space-between;
        padding: .6rem 0;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    .info-item:last-child { border-bottom: none; }
    .info-item span:first-child { color: #aaa; font-size: .9rem; }

    .social-link {
        color: #ffcc00;
        text-decoration: none;
        word-break: break-all;
    }
    .social-link:hover {
        text-decoration: underline;
    }

    .edit-form {
        display: none;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(255, 204, 0, 0.2);
        animation: fadeIn 0.3s ease;
    }

    .global-form-actions {
        display: none;
        justify-content: center;
        gap: 0.75rem;
        margin-top: 2rem;
        padding: 1.5rem;
        background: linear-gradient(145deg, #242424, #2a2a2a);
        border-radius: 15px;
        border: 1px solid #333;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .form-group { margin-bottom: 1rem; }
    .form-label { color: #ddd; font-weight: 500; margin-bottom: 0.5rem; display: block; }
    .form-control, .form-select {
        background-color: #1a1a1a;
        border: 1px solid #444;
        color: #fff;
        border-radius: 8px;
        padding: 0.75rem;
        transition: all 0.3s ease;
        width: 100%;
    }
    .form-control:focus, .form-select:focus {
        background-color: #1f1f1f;
        border-color: #ffcc00;
        box-shadow: 0 0 0 0.25rem rgba(255, 204, 0, 0.25);
        color: #fff;
        outline: none;
    }

    .btn-cancel {
        background-color: #444;
        border: 1px solid #444;
        color: #fff;
        border-radius: 8px;
        padding: 0.6rem 1.2rem;
        font-weight: 500;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .btn-cancel:hover { background-color: #555; }

    .btn-save {
        background-color: #ffcc00;
        border: 1px solid #ffcc00;
        color: #121212;
        border-radius: 8px;
        padding: 0.6rem 1.2rem;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .btn-save:hover {
        background-color: #e6b800;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 204, 0, 0.3);
    }

    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 1rem;
        border: 1px solid transparent;
        max-width: 1000px;
        width: 100%;
    }
    .alert-success {
        background-color: rgba(40, 167, 69, 0.2);
        border-color: rgba(40, 167, 69, 0.3);
        color: #28a745;
    }
    .alert-warning {
        background-color: rgba(255, 193, 7, 0.2);
        border-color: rgba(255, 193, 7, 0.3);
        color: #ffc107;
    }
    .alert-error {
        background-color: rgba(220, 53, 69, 0.2);
        border-color: rgba(220, 53, 69, 0.3);
        color: #dc3545;
    }

    @media (max-width: 768px) {
        .profile-grid { grid-template-columns: 1fr; gap: 1.5rem; }
        .global-form-actions { flex-direction: column; }
    }
</style>

<div class="profile-wrapper">

    {{-- Header --}}
    <div class="profile-header">
        <img src="{{ $profile?->photo ? asset('storage/' . $profile->photo) : asset('images/default-user.png') }}" alt="Foto Profil">
        <h2>{{ $profile?->owner_name ?? $owner->name ?? 'Owner' }}</h2>
    </div>

    {{-- Notifikasi --}}
    @if(!$profile)
        <div class="alert alert-warning">
            ⚠️ Data profil Anda belum lengkap. Silakan lengkapi informasi berikut.
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">✅ {{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            ❌ Terjadi kesalahan:
            <ul style="margin: 0; padding-left: 1rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('profile.owner.update') }}" method="POST" enctype="multipart/form-data" style="width: 100%; max-width: 1000px;">
        @csrf

        <div class="profile-grid">

            {{-- Informasi Bisnis & Login --}}
            <div class="profile-section">
                <div class="section-header">
                    <h3>Informasi Bisnis & Login</h3>
                    <button type="button" class="edit-btn" onclick="toggleEditForm('businessInfo')">Edit</button>
                </div>

                <div id="businessInfoDisplay">
                    <div class="info-item"><span>Username</span><span>{{ $owner->name ?? '-' }}</span></div>
                    <div class="info-item"><span>Email Login</span><span>{{ $owner->email ?? '-' }}</span></div>
                    <div class="info-item"><span>Nama Bisnis</span><span>{{ $profile?->business_name ?? '-' }}</span></div>
                    <div class="info-item"><span>Nama Pemilik</span><span>{{ $profile?->owner_name ?? '-' }}</span></div>
                    <div class="info-item"><span>No. Telepon</span><span>{{ $profile?->phone_number ?? '-' }}</span></div>
                </div>

                <div id="businessInfoForm" class="edit-form">
                    <div class="form-group">
                        <label class="form-label">Username *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $owner->name ?? '') }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Login *</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $owner->email ?? '') }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nama Bisnis</label>
                        <input type="text" name="business_name" class="form-control" value="{{ old('business_name', $profile?->business_name ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nama Pemilik *</label>
                        <input type="text" name="owner_name" class="form-control" value="{{ old('owner_name', $profile?->owner_name ?? '') }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $profile?->phone_number ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Foto Profil</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        @if($profile?->photo)
                            <small style="color: #aaa;">Foto saat ini: {{ basename($profile->photo) }}</small>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Alamat Bisnis --}}
            <div class="profile-section">
                <div class="section-header">
                    <h3>Alamat Bisnis</h3>
                    <button type="button" class="edit-btn" onclick="toggleEditForm('addressInfo')">Edit</button>
                </div>

                <div id="addressInfoDisplay">
                    <div class="info-item"><span>Alamat</span><span>{{ $profile?->address ?? '-' }}</span></div>
                    <div class="info-item"><span>Kota</span><span>{{ $profile?->city ?? '-' }}</span></div>
                    <div class="info-item"><span>Provinsi</span><span>{{ $profile?->province ?? '-' }}</span></div>
                </div>

                <div id="addressInfoForm" class="edit-form">
                    <div class="form-group">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control" rows="3">{{ old('address', $profile?->address ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kota</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $profile?->city ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Provinsi</label>
                        <input type="text" name="province" class="form-control" value="{{ old('province', $profile?->province ?? '') }}">
                    </div>
                </div>
            </div>

            {{-- Legal & Bank --}}
            <div class="profile-section">
                <div class="section-header">
                    <h3>Legal & Bank</h3>
                    <button type="button" class="edit-btn" onclick="toggleEditForm('legalInfo')">Edit</button>
                </div>

                <div id="legalInfoDisplay">
                    <div class="info-item"><span>No. KTP</span><span>{{ $profile?->ktp_number ? '***' . substr($profile->ktp_number, -4) : '-' }}</span></div>
                    <div class="info-item"><span>No. NPWP</span><span>{{ $profile?->npwp_number ?? '-' }}</span></div>
                    <div class="info-item"><span>Akun Bank</span><span>{{ $profile?->bank_account ? '***' . substr($profile->bank_account, -4) : '-' }}</span></div>
                </div>

                <div id="legalInfoForm" class="edit-form">
                    <div class="form-group">
                        <label class="form-label">No. KTP</label>
                        <input type="text" name="ktp_number" class="form-control" value="{{ old('ktp_number', $profile?->ktp_number ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">No. NPWP</label>
                        <input type="text" name="npwp_number" class="form-control" value="{{ old('npwp_number', $profile?->npwp_number ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Akun Bank</label>
                        <input type="text" name="bank_account" class="form-control" value="{{ old('bank_account', $profile?->bank_account ?? '') }}">
                    </div>
                </div>
            </div>

            {{-- Social Media --}}
            <div class="profile-section">
                <div class="section-header">
                    <h3>Media Sosial</h3>
                    <button type="button" class="edit-btn" onclick="toggleEditForm('socialInfo')">Edit</button>
                </div>

                <div id="socialInfoDisplay">
                    <div class="info-item">
                        <span>Facebook</span>
                        <span>
                            @if($profile?->facebook)
                                <a href="{{ $profile->facebook }}" target="_blank" class="social-link">
                                    {{ Str::limit($profile->facebook, 30) }}
                                </a>
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span>Instagram</span>
                        <span>
                            @if($profile?->instagram)
                                <a href="{{ $profile->instagram }}" target="_blank" class="social-link">
                                    {{ Str::limit($profile->instagram, 30) }}
                                </a>
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span>TikTok</span>
                        <span>
                            @if($profile?->tiktok)
                                <a href="{{ $profile->tiktok }}" target="_blank" class="social-link">
                                    {{ Str::limit($profile->tiktok, 30) }}
                                </a>
                            @else
                                -
                            @endif
                        </span>
                    </div>
                </div>

                <div id="socialInfoForm" class="edit-form">
                    <div class="form-group">
                        <label class="form-label">Facebook URL</label>
                        <input type="url" name="facebook" class="form-control" placeholder="https://facebook.com/username" value="{{ old('facebook', $profile?->facebook ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Instagram URL</label>
                        <input type="url" name="instagram" class="form-control" placeholder="https://instagram.com/username" value="{{ old('instagram', $profile?->instagram ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">TikTok URL</label>
                        <input type="url" name="tiktok" class="form-control" placeholder="https://tiktok.com/@username" value="{{ old('tiktok', $profile?->tiktok ?? '') }}">
                    </div>
                </div>
            </div>

            {{-- Informasi Akun --}}
            <div class="profile-section">
                <div class="section-header">
                    <h3>Informasi Akun</h3>
                </div>

                <div id="loginInfoDisplay">
                    <div class="info-item"><span>Role</span><span style="text-transform: capitalize;">{{ $owner->role ?? '-' }}</span></div>
                    <div class="info-item"><span>Terdaftar Sejak</span><span>{{ $owner->created_at->format('d M Y') ?? '-' }}</span></div>
                    <div class="info-item"><span>Terakhir Diupdate</span><span>{{ $owner->updated_at->format('d M Y H:i') ?? '-' }}</span></div>
                </div>
            </div>
        </div>

        {{-- Tombol Submit --}}
        <div id="globalFormActions" class="global-form-actions">
            <button type="button" class="btn-cancel" onclick="hideAllForms()">Batal</button>
            <button type="submit" class="btn-save">Simpan Semua Perubahan</button>
        </div>
    </form>
</div>

<script>
    function toggleEditForm(section) {
        const display = document.getElementById(section + 'Display');
        const form = document.getElementById(section + 'Form');
        const globalActions = document.getElementById('globalFormActions');
        const isVisible = form.style.display === 'block';

        form.style.display = isVisible ? 'none' : 'block';
        display.style.display = isVisible ? 'block' : 'none';
        globalActions.style.display = isVisible ? 'none' : 'flex';
    }

    function hideAllForms() {
        document.querySelectorAll('.edit-form').forEach(f => f.style.display = 'none');
        document.querySelectorAll('[id$="Display"]').forEach(d => d.style.display = 'block');
        document.getElementById('globalFormActions').style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', () => {
        hideAllForms();
        setTimeout(() => document.querySelectorAll('.alert').forEach(a => a.remove()), 5000);
    });
</script>
@endsection
