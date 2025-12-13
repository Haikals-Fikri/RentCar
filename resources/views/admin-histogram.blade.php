@extends('dashboard-admin')

@vite('resources/css/admin/A_Histogram.css')

@section('content')

@if(isset($UserCount) && isset($OwnerCount))

<div class="chart-container">
    <h3>
        Statistik Pengguna dan Booking
    </h3>
    <p>
        Grafik menampilkan jumlah pengguna dan pemilik terdaftar.
    </p>

    <div class="chart-wrapper">
        <canvas
            id="userOwnerChart"
            data-users="{{ $UserCount }}"
            data-owners="{{ $OwnerCount }}">
        </canvas>
    </div>
</div>
@vite('resources/js/admin/A_Histogram.js')
@endif

@endsection
