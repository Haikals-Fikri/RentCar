@extends('layouts.owner.owner')

@section('title', 'Profil Owner')
@section('page-title', 'Profil Saya')

@push('styles')
@vite([
'resources/css/owner/profile-owner.css'
])
@endpush

@section('content')
@include('layouts.owner.partials.header', ['headerTitle' => 'Profil Saya'])

<div class="profile-wrapper">


<div class="profile-header">
    <img class="profile-header-img" src="{{ $profile?->photo ? asset('storage/' . $profile->photo) : asset('images/default-user.png') }}" alt="Foto Profil">
    <h2 class="profile-header-name">{{ $profile?->owner_name ?? $owner->name ?? 'Owner' }}</h2>
</div>

@if(!$profile)
    <div class="profile-alert profile-alert-warning">
        Data profil Anda belum lengkap. Silakan lengkapi informasi berikut.
    </div>
@endif

@if(session('success'))
    <div class="profile-alert profile-alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="profile-alert profile-alert-error">
        Terjadi kesalahan:
        <ul class="profile-error-list">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('profile.owner.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="profile-grid">

        <div class="profile-section">
            <div class="profile-section-header">
                <h3>Informasi Bisnis & Login</h3>
                <button type="button" class="profile-btn-edit">Edit</button>
            </div>

            <div id="businessInfoDisplay" class="profile-display">
                <div class="profile-info-item"><span>Username</span><span>{{ $owner->name ?? '-' }}</span></div>
                <div class="profile-info-item"><span>Email Login</span><span>{{ $owner->email ?? '-' }}</span></div>
                <div class="profile-info-item"><span>Nama Bisnis</span><span>{{ $profile?->business_name ?? '-' }}</span></div>
                <div class="profile-info-item"><span>Nama Pemilik</span><span>{{ $profile?->owner_name ?? '-' }}</span></div>
                <div class="profile-info-item"><span>No. Telepon</span><span>{{ $profile?->phone_number ?? '-' }}</span></div>
            </div>

            <div id="businessInfoForm" class="profile-edit-form">
                <div class="profile-form-group">
                    <label>Username *</label>
                    <input type="text" name="name" class="profile-form-control" value="{{ old('name', $owner->name ?? '') }}" required>
                </div>

                <div class="profile-form-group">
                    <label>Email Login *</label>
                    <input type="email" name="email" class="profile-form-control" value="{{ old('email', $owner->email ?? '') }}" required>
                </div>

                <div class="profile-form-group">
                    <label>Nama Bisnis</label>
                    <input type="text" name="business_name" class="profile-form-control" value="{{ old('business_name', $profile?->business_name ?? '') }}">
                </div>

                <div class="profile-form-group">
                    <label>Nama Pemilik *</label>
                    <input type="text" name="owner_name" class="profile-form-control" value="{{ old('owner_name', $profile?->owner_name ?? '') }}" required>
                </div>

                <div class="profile-form-group">
                    <label>No. Telepon</label>
                    <input type="text" name="phone_number" class="profile-form-control" value="{{ old('phone_number', $profile?->phone_number ?? '') }}">
                </div>

                <div class="profile-form-group">
                    <label>Foto Profil</label>
                    <input type="file" name="photo" class="profile-form-control" accept="image/*">
                    @if($profile?->photo)
                        <div class="profile-photo-note">Foto saat ini: {{ basename($profile->photo) }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="profile-section">
            <div class="profile-section-header">
                <h3>Alamat Bisnis</h3>
                <button type="button" class="profile-btn-edit">Edit</button>
            </div>

            <div id="addressInfoDisplay" class="profile-display">
                <div class="profile-info-item"><span>Alamat</span><span>{{ $profile?->address ?? '-' }}</span></div>
                <div class="profile-info-item"><span>Kota</span><span>{{ $profile?->city ?? '-' }}</span></div>
                <div class="profile-info-item"><span>Provinsi</span><span>{{ $profile?->province ?? '-' }}</span></div>
            </div>

            <div id="addressInfoForm" class="profile-edit-form">
                <div class="profile-form-group">
                    <label>Alamat</label>
                    <textarea name="address" class="profile-form-control" rows="3">{{ old('address', $profile?->address ?? '') }}</textarea>
                </div>

                <div class="profile-form-group">
                    <label>Kota</label>
                    <input type="text" name="city" class="profile-form-control" value="{{ old('city', $profile?->city ?? '') }}">
                </div>

                <div class="profile-form-group">
                    <label>Provinsi</label>
                    <input type="text" name="province" class="profile-form-control" value="{{ old('province', $profile?->province ?? '') }}">
                </div>
            </div>
        </div>

        <div class="profile-section">
            <div class="profile-section-header">
                <h3>Legal & Bank</h3>
                <button type="button" class="profile-btn-edit">Edit</button>
            </div>

            <div id="legalInfoDisplay" class="profile-display">
                <div class="profile-info-item"><span>No. KTP</span><span>{{ $profile?->ktp_number ? '***' . substr($profile->ktp_number, -4) : '-' }}</span></div>
                <div class="profile-info-item"><span>No. NPWP</span><span>{{ $profile?->npwp_number ?? '-' }}</span></div>
                <div class="profile-info-item"><span>Akun Bank</span><span>{{ $profile?->bank_account ? '***' . substr($profile->bank_account, -4) : '-' }}</span></div>
            </div>

            <div id="legalInfoForm" class="profile-edit-form">
                <div class="profile-form-group">
                    <label>No. KTP</label>
                    <input type="text" name="ktp_number" class="profile-form-control" value="{{ old('ktp_number', $profile?->ktp_number ?? '') }}">
                </div>

                <div class="profile-form-group">
                    <label>No. NPWP</label>
                    <input type="text" name="npwp_number" class="profile-form-control" value="{{ old('npwp_number', $profile?->npwp_number ?? '') }}">
                </div>

                <div class="profile-form-group">
                    <label>Akun Bank</label>
                    <input type="text" name="bank_account" class="profile-form-control" value="{{ old('bank_account', $profile?->bank_account ?? '') }}">
                </div>
            </div>
        </div>

        <div class="profile-section">
            <div class="profile-section-header">
                <h3>Media Sosial</h3>
                <button type="button" class="profile-btn-edit">Edit</button>
            </div>

            <div id="socialInfoDisplay" class="profile-display">
                <div class="profile-info-item">
                    <span>Facebook</span>
                    <span>
                        @if($profile?->facebook)
                            <a href="{{ $profile->facebook }}" target="_blank" class="profile-social-link">{{ Str::limit($profile->facebook, 30) }}</a>
                        @else
                            -
                        @endif
                    </span>
                </div>

                <div class="profile-info-item">
                    <span>Instagram</span>
                    <span>
                        @if($profile?->instagram)
                            <a href="{{ $profile->instagram }}" target="_blank" class="profile-social-link">{{ Str::limit($profile->instagram, 30) }}</a>
                        @else
                            -
                        @endif
                    </span>
                </div>

                <div class="profile-info-item">
                    <span>TikTok</span>
                    <span>
                        @if($profile?->tiktok)
                            <a href="{{ $profile->tiktok }}" target="_blank" class="profile-social-link">{{ Str::limit($profile->tiktok, 30) }}</a>
                        @else
                            -
                        @endif
                    </span>
                </div>
            </div>

            <div id="socialInfoForm" class="profile-edit-form">
                <div class="profile-form-group">
                    <label>Facebook URL</label>
                    <input type="url" name="facebook" class="profile-form-control" value="{{ old('facebook', $profile?->facebook ?? '') }}">
                </div>

                <div class="profile-form-group">
                    <label>Instagram URL</label>
                    <input type="url" name="instagram" class="profile-form-control" value="{{ old('instagram', $profile?->instagram ?? '') }}">
                </div>

                <div class="profile-form-group">
                    <label>TikTok URL</label>
                    <input type="url" name="tiktok" class="profile-form-control" value="{{ old('tiktok', $profile?->tiktok ?? '') }}">
                </div>
            </div>
        </div>

        <div class="profile-section">
            <div class="profile-section-header">
                <h3>Informasi Akun</h3>
            </div>

            <div class="profile-display">
                <div class="profile-info-item"><span>Role</span><span style="text-transform: capitalize;">{{ $owner->role ?? '-' }}</span></div>
                <div class="profile-info-item"><span>Terdaftar Sejak</span><span>{{ $owner->created_at->format('d M Y') ?? '-' }}</span></div>
                <div class="profile-info-item"><span>Terakhir Diupdate</span><span>{{ $owner->updated_at->format('d M Y H:i') ?? '-' }}</span></div>
            </div>
        </div>
    </div>

    <div id="globalFormActions" class="profile-global-form-actions">
        <button type="button" class="profile-btn-cancel">Batal</button>
        <button type="submit" class="profile-btn-save">Simpan Semua Perubahan</button>
    </div>
</form>

</div>


@endsection
@push('scripts')
@vite([
'resources/js/owner/profile-owner.js'
])

@endpush
