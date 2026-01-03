<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    /**
     * Menampilkan dashboard owner dengan daftar kendaraan.
     */
    public function index()
    {
        $ownerId = session('owner_id');
        if (!$ownerId) {
            return redirect('/login-owner')->withErrors('Silakan login sebagai Owner.');
        }

        $vehicles = Vehicle::where('owner_id', $ownerId)->latest()->get();
        return view('layouts.owner.dashboard-owner', compact('vehicles'));
    }

    /**
     * Menampilkan form untuk membuat kendaraan baru.
     */
    public function create()
    {
        return view('layouts.owner.owner-vehicle-create');
    }

    /**
     * Simpan kendaraan baru.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'          => 'required|string|max:255',
            'brand'         => 'required|string|max:255',
            'type'          => 'required|string|max:2555',
            'plate_number'  => 'required|string|unique:vehicles',
            'seat'          => 'nullable|string|max:255',
            'transmission'  => 'nullable|string|max:255',
            'fuel_type'     => 'nullable|string|max:255',
            'year'          => 'nullable|string|max:4',
            'price_per_day' => 'required|numeric|min:0',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $validatedData['image'] = $request->file('image')->store('vehicles', 'public');
        }

        $validatedData['owner_id'] = session('owner_id');
        Vehicle::create($validatedData);

        return redirect()->route('owner.dashboard')->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit kendaraan.
     */
    public function edit($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        return view('layouts.owner.owner-vehicle-edit', compact('vehicle'));
    }

    /**
     * Update data kendaraan.
     */
    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $validatedData = $request->validate([
            'name'          => 'required|string|max:255',
            'brand'         => 'required|string|max:255',
            'type'          => 'required|string|max:255',
            'plate_number'  => 'required|string|unique:vehicles,plate_number,' . $id,
            'seat'          => 'nullable|string|max:255',
            'transmission'  => 'nullable|string|max:255',
            'fuel_type'     => 'nullable|string|max:255',
            'year'          => 'nullable|string|max:4',
            'price_per_day' => 'required|numeric|min:0',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('image')) {
            if ($vehicle->image) {
                Storage::disk('public')->delete($vehicle->image);
            }
            $validatedData['image'] = $request->file('image')->store('vehicles', 'public');
        }

        $vehicle->update($validatedData);

        return redirect()->route('owner.dashboard')->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Vehicle $vehicle)
    {
        $validatedData = $request->validate([
            'status_vehicle' => 'required|in:Tersedia,Tidak_tersedia,Maintanance'
        ]);

        $vehicle->update([
            'status_vehicle' => $validatedData['status_vehicle']
        ]);

        return redirect()->route('owner.dashboard')->with('success', 'Status kendaraan berhasil diperbarui.');
    }

    /**
     * Hapus kendaraan.
     */
    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);

        if ($vehicle->image) {
            Storage::disk('public')->delete($vehicle->image);
        }

        $vehicle->delete();
        return redirect()->route('owner.dashboard')->with('success', 'Kendaraan berhasil dihapus.');
    }
}
