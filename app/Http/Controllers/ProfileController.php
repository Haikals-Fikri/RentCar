<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserProfile;
use App\Models\OwnerProfile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * 🔹 Tampilkan profil user
     */
    public function showUserProfile()
    {
        $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->first();

        return view('layouts.user.profile', compact('user', 'profile'));
    }

    /**
     * 🔹 Update atau buat profil user
     */
    public function updateUserProfile(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        // ===== VALIDASI =====
        $validated = $request->validate([
            'name'          => 'required|string|max:50|unique:users,name,' . $user->id,
            'full_name'     => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone_number'  => 'nullable|string|max:20',
            'address'       => 'nullable|string',
            'city'          => 'nullable|string',
            'province'      => 'nullable|string',
            'postal_code'   => 'nullable|string|max:10',
            'date_of_birth' => 'nullable|date',
            'gender'        => 'nullable|in:Laki-laki,Perempuan',
            'photo'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // ===== UPDATE USER =====
        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        // ===== DATA PROFIL =====
        $profileData = [
            'full_name'     => $validated['full_name'],
            'phone_number'  => $validated['phone_number'] ?? null,
            'address'       => $validated['address'] ?? null,
            'city'          => $validated['city'] ?? null,
            'province'      => $validated['province'] ?? null,
            'postal_code'   => $validated['postal_code'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender'        => $validated['gender'] ?? null,
        ];

        // ===== FOTO =====
        if ($request->hasFile('photo')) {
            $existing = UserProfile::where('user_id', $user->id)->first();

            if ($existing && $existing->photo) {
                Storage::disk('public')->delete($existing->photo);
            }

            $path = $request->file('photo')->store('UserProfile', 'public');
            $profileData['photo'] = $path;
        }

        // ===== SIMPAN =====
        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return redirect()->route('user.profile')
                         ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * 🔹 Tampilkan profil owner
     */
    public function showOwnerProfile()
    {
        $owner = Auth::user();
        $profile = OwnerProfile::where('owner_id', $owner->id)->first();

        return view('layouts.owner.owner-profile', compact('owner', 'profile'));
    }

    /**
     * 🔹 Update atau buat profil owner
     */
    public function updateOwnerProfile(Request $request)
    {
        $owner = User::findOrFail(Auth::id());

        if ($owner->role !== 'owner') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        // ===== VALIDASI =====
        $validated = $request->validate([
            'name'           => 'required|string|max:50|unique:users,name,' . $owner->id,
            'email'          => 'required|email|max:255|unique:users,email,' . $owner->id,
            'owner_name'     => 'required|string|max:255',
            'business_name'  => 'nullable|string|max:255',
            'phone_number'   => 'nullable|string|max:20',
            'address'        => 'nullable|string',
            'city'           => 'nullable|string',
            'province'       => 'nullable|string',
            'ktp_number'     => 'nullable|string|max:50',
            'npwp_number'    => 'nullable|string|max:50',
            'bank_account'   => 'nullable|string|max:100',
            'facebook'       => 'nullable|url|max:255',
            'instagram'      => 'nullable|url|max:255',
            'tiktok'         => 'nullable|url|max:255',
            'photo'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // ===== UPDATE USER =====
        $owner->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        // ===== PROFILE DATA =====
        $profileData = [
            'owner_name'    => $validated['owner_name'],
            'business_name' => $validated['business_name'] ?? null,
            'phone_number'  => $validated['phone_number'] ?? null,
            'address'       => $validated['address'] ?? null,
            'city'          => $validated['city'] ?? null,
            'province'      => $validated['province'] ?? null,
            'ktp_number'    => $validated['ktp_number'] ?? null,
            'npwp_number'   => $validated['npwp_number'] ?? null,
            'bank_account'  => $validated['bank_account'] ?? null,
            'facebook'      => $validated['facebook'] ?? null,
            'instagram'     => $validated['instagram'] ?? null,
            'tiktok'        => $validated['tiktok'] ?? null,
        ];

        // ===== FOTO =====
        if ($request->hasFile('photo')) {
            $existing = OwnerProfile::where('owner_id', $owner->id)->first();

            if ($existing && $existing->photo) {
                Storage::disk('public')->delete($existing->photo);
            }

            $photoPath = $request->file('photo')->store('OwnerProfile', 'public');
            $profileData['photo'] = $photoPath;
        }

        // ===== SIMPAN =====
        OwnerProfile::updateOrCreate(
            ['owner_id' => $owner->id],
            $profileData
        );

        return redirect()->route('profile.owner')
                         ->with('success', 'Profil owner berhasil diperbarui.');
    }

    /**
     * 🔹 Owner profile untuk user
     */
    public function showOwnerProfileForUser($id)
    {
        $owner = User::with(['vehicles' => function($query) {
            $query->withAvg('bookings as avg_rating', 'rating');
        }])->findOrFail($id);

        return view('layouts.user.user-owner-profile', compact('owner'));
    }
}
