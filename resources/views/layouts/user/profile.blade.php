@extends('layouts.user.user')

@section('content')
@vite('resources/css/user/profiluser.css')


<div class="profile-wrapper">

    {{-- Header --}}
    <div class="profile-header">
        <img src="{{ $profile?->photo ? asset('storage/' . $profile->photo) : asset('images/default-user.png') }}" alt="Foto Profil">
        <h2>{{ $profile?->full_name ?? $user->name ?? 'Anonim' }}</h2>
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

    {{-- Alert Validasi --}}
    @if($errors->any())
        <div class="alert alert-error">
            ⚠️ <strong>Masukkan Data Yang Sesuai</strong>
            <ul style="margin: 0.5rem 0 0 0; padding-left: 1rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

        {{-- Auto open form sections with errors --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Check which fields have errors
                const errorFields = [
                    @foreach($errors->keys() as $key)
                        '{{ $key }}',
                    @endforeach
                ];

                // Open relevant form sections
                if (errorFields.some(field =>
                    ['full_name', 'name', 'date_of_birth', 'gender'].includes(field)
                )) {
                    toggleEditForm('basicInfo');
                }

                if (errorFields.some(field =>
                    ['email', 'phone_number', 'address', 'city', 'province', 'postal_code', 'photo'].includes(field)
                )) {
                    toggleEditForm('contactInfo');
                }

                // Add invalid classes to fields with errors
                @foreach($errors->keys() as $key)
                    const input{{ $key }} = document.querySelector('[name="{{ $key }}"]');
                    if (input{{ $key }}) {
                        input{{ $key }}.classList.add('is-invalid');
                    }
                @endforeach
            });
        </script>
    @endif

    {{-- Form --}}
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" style="width: 100%; max-width: 1000px;">
        @csrf
        @method('PUT')

        <div class="profile-grid">

            {{-- Informasi Dasar --}}
            <div class="profile-section">
                <div class="section-header">
                    <h3>Informasi Dasar</h3>
                    <button type="button" class="edit-btn" onclick="toggleEditForm('basicInfo')">Edit</button>
                </div>

                <div id="basicInfoDisplay">
                    <div class="info-item"><span>Nama Lengkap</span><span>{{ $profile?->full_name ?? '-' }}</span></div>
                    <div class="info-item"><span>Username</span><span>{{ $user->name ?? '-' }}</span></div>
                    <div class="info-item"><span>Tanggal Lahir</span><span>{{ optional($profile)->date_of_birth ? \Carbon\Carbon::parse($profile->date_of_birth)->format('d/m/Y') : '-' }}</span></div>
                    <div class="info-item"><span>Jenis Kelamin</span><span>{{ $profile?->gender ?? '-' }}</span></div>
                </div>

                <div id="basicInfoForm" class="edit-form">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="full_name" class="form-control {{ $errors->has('full_name') ? 'is-invalid' : '' }}"
                               value="{{ old('full_name', $profile?->full_name ?? '') }}" required>
                        @error('full_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Username *</label>
                        <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                               value="{{ old('name', $user->name ?? '') }}" required>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="date_of_birth" class="form-control {{ $errors->has('date_of_birth') ? 'is-invalid' : '' }}"
                               value="{{ old('date_of_birth', $profile?->date_of_birth ?? '') }}">
                        @error('date_of_birth')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="gender" class="form-select {{ $errors->has('gender') ? 'is-invalid' : '' }}">
                            <option value="">Pilih</option>
                            <option value="Laki-laki" {{ (old('gender', $profile?->gender ?? '') == 'Laki-laki') ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ (old('gender', $profile?->gender ?? '') == 'Perempuan') ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Kontak & Alamat --}}
            <div class="profile-section">
                <div class="section-header">
                    <h3>Kontak & Alamat</h3>
                    <button type="button" class="edit-btn" onclick="toggleEditForm('contactInfo')">Edit</button>
                </div>

                <div id="contactInfoDisplay">
                    <div class="info-item"><span>Email</span><span>{{ $user->email }}</span></div>
                    <div class="info-item"><span>No. Telepon</span><span>{{ $profile?->phone_number ?? '-' }}</span></div>
                    <div class="info-item"><span>Alamat</span><span>{{ $profile?->address ?? '-' }}</span></div>
                    <div class="info-item"><span>Kota</span><span>{{ $profile?->city ?? '-' }}</span></div>
                    <div class="info-item"><span>Provinsi</span><span>{{ $profile?->province ?? '-' }}</span></div>
                    <div class="info-item"><span>Kode Pos</span><span>{{ $profile?->postal_code ?? '-' }}</span></div>
                </div>

                <div id="contactInfoForm" class="edit-form">
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                               value="{{ old('email', $user->email ?? '') }}" required>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="phone_number" class="form-control {{ $errors->has('phone_number') ? 'is-invalid' : '' }}"
                               value="{{ old('phone_number', $profile?->phone_number ?? '') }}">
                        @error('phone_number')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control {{ $errors->has('address') ? 'is-invalid' : '' }}"
                                  rows="3">{{ old('address', $profile?->address ?? '') }}</textarea>
                        @error('address')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kota</label>
                        <input type="text" name="city" class="form-control {{ $errors->has('city') ? 'is-invalid' : '' }}"
                               value="{{ old('city', $profile?->city ?? '') }}">
                        @error('city')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Provinsi</label>
                        <input type="text" name="province" class="form-control {{ $errors->has('province') ? 'is-invalid' : '' }}"
                               value="{{ old('province', $profile?->province ?? '') }}">
                        @error('province')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kode Pos</label>
                        <input type="text" name="postal_code" class="form-control {{ $errors->has('postal_code') ? 'is-invalid' : '' }}"
                               value="{{ old('postal_code', $profile?->postal_code ?? '') }}">
                        @error('postal_code')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Foto Profil</label>
                        <input type="file" name="photo" class="form-control {{ $errors->has('photo') ? 'is-invalid' : '' }}"
                               accept="image/*">
                        @error('photo')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        @if($profile?->photo)
                            <small style="color: #aaa; display: block; margin-top: 0.5rem;">
                                Foto saat ini: {{ basename($profile->photo) }}
                            </small>
                        @endif
                    </div>
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
    let activeForms = new Set();

    function toggleEditForm(section) {
        const display = document.getElementById(section + 'Display');
        const form = document.getElementById(section + 'Form');
        const globalActions = document.getElementById('globalFormActions');
        const isVisible = form.style.display === 'block';

        form.style.display = isVisible ? 'none' : 'block';
        display.style.display = isVisible ? 'block' : 'none';

        if (isVisible) {
            activeForms.delete(section);
        } else {
            activeForms.add(section);
        }

        if (activeForms.size > 0) {
            globalActions.style.display = 'flex';
        } else {
            globalActions.style.display = 'none';
        }
    }

    function hideAllForms() {
        document.querySelectorAll('.edit-form').forEach(f => f.style.display = 'none');
        document.querySelectorAll('[id$="Display"]').forEach(d => d.style.display = 'block');
        document.getElementById('globalFormActions').style.display = 'none';
        activeForms.clear();

        // Remove validation styling
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.error-message').forEach(el => el.remove());
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Auto hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // If there are errors, show the global actions
        @if($errors->any())
            document.getElementById('globalFormActions').style.display = 'flex';
        @else
            hideAllForms();
        @endif
    });

    // Real-time validation
    document.querySelectorAll('input, select, textarea').forEach(element => {
        element.addEventListener('blur', function() {
            validateField(this);
        });

        element.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    });

    function validateField(field) {
        const errorSpan = field.nextElementSibling;

        // Remove existing error if any
        if (errorSpan && errorSpan.classList.contains('error-message')) {
            errorSpan.remove();
        }
        field.classList.remove('is-invalid');

        // Basic validation
        if (field.hasAttribute('required') && !field.value.trim()) {
            showFieldError(field, 'Masukan Data Sesuai');
            return false;
        }

        if (field.type === 'email' && field.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(field.value)) {
                showFieldError(field, 'Format email tidak valid');
                return false;
            }
        }

        return true;
    }

    function showFieldError(field, message) {
        field.classList.add('is-invalid');
        const errorSpan = document.createElement('span');
        errorSpan.className = 'error-message';
        errorSpan.textContent = message;
        field.parentNode.appendChild(errorSpan);
    }
</script>
@endsection
