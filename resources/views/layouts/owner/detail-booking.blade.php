@extends('layouts.owner.owner')
@section('title', 'Dashboard Booking')

@push('styles')
    @vite('resources/css/owner/Detail_booking.css')
@endpush

@section('content')
    @include('layouts.owner.partials.header', ['headerTitle' => 'Dashboard Booking'])

    <div class="dashboard-container-detail">

        <!-- Header Actions -->
        <div class="header-actions-wrapper-detail">
            <div class="header-actions-detail">
                <form method="GET" action="{{ route('booking.analytics') }}" class="filter-form-detail">
                    <label>Lihat data:</label>
                    <select name="filter">
                        <option value="mingguan" {{ request('filter') == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                        <option value="bulanan" {{ request('filter') == 'bulanan' || !request('filter') ? 'selected' : '' }}>Bulanan</option>
                        <option value="tahunan" {{ request('filter') == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                        <option value="custom" {{ request('filter') == 'custom' ? 'selected' : '' }}>Tanggal Tertentu</option>
                    </select>

                    @if(request('filter') == 'custom')
                        <input type="date" name="date" value="{{ request('date') }}" />
                    @endif

                    @if(in_array(request('filter'), ['bulanan', 'tahunan']) || !request('filter'))
                        <select name="month">
                            @foreach($dropdownOptions['months'] as $key => $month)
                                <option value="{{ $key }}" {{ request('month', date('m')) == $key ? 'selected' : '' }}>{{ $month }}</option>
                            @endforeach
                        </select>
                        <select name="year">
                            @foreach($dropdownOptions['years'] as $year)
                                <option value="{{ $year }}" {{ request('year', date('Y')) == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    @endif
                    <button type="submit">Terapkan</button>
                </form>

                <a href="{{ route('booking.print-report') }}?filter={{ request('filter', 'bulanan') }}&date={{ request('date') }}&month={{ request('month', date('m')) }}&year={{ request('year', date('Y')) }}&compare_mode={{ request('compare_mode') }}&periodA={{ request('periodA') }}&periodB={{ request('periodB') }}"
                   class="print-button"
                   target="_blank"
                   title="Cetak Laporan">
                    🖨️ Print Laporan
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content-detail">

            <!-- Charts Section -->
            <div class="charts-section-detail">
                <div class="chart-area-detail">
                    <h2>Distribusi Booking</h2>
                    <div class="chart-wrapper-detail">
                        <canvas id="mainChart"></canvas>
                    </div>
                    <div class="chart-summary-detail">
                        <div>
                            <p>Total Booking</p>
                            <div>{{ $mainData['stats']['total'] }}</div>
                        </div>
                        <div>
                            <p>Rata-rata</p>
                            <div>{{ $mainData['stats']['average'] }}</div>
                        </div>
                        <div>
                            <p>Puncak</p>
                            <div>{{ $mainData['stats']['peak_label'] }} ({{ $mainData['stats']['peak'] }})</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Table -->
            <div class="booking-table-detail">
                <h3>Daftar Booking</h3>
                <p>Menampilkan {{ count($mainData['table_data']) }} data booking</p>
                <div class="table-wrapper-detail">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama</th>
                                <th>Mobil</th>
                                <th>Durasi</th>
                                <th>Tipe</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mainData['table_data'] as $booking)
                                <tr>
                                    <td>{{ $booking['tanggal'] }}</td>
                                    <td>{{ $booking['nama'] }}</td>
                                    <td>{{ $booking['mobil'] }}</td>
                                    <td>{{ $booking['durasi'] }}</td>
                                    <td>{{ $booking['tipe'] }}</td>
                                    <td class="status-{{ strtolower($booking['status']) }}">{{ $booking['status'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">Tidak ada data booking untuk periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="insight-detail">
                <h3>Insight Otomatis</h3>
                <ul>
                    @foreach($insights as $insight)
                        <li>{!! $insight !!}</li>
                    @endforeach
                </ul>
            </div>

        </div>
    </div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const labels = @json($mainData['labels'] ?? []);
    const data = @json($mainData['data'] ?? []);

    if(labels.length && data.length){
        const ctx = document.getElementById('mainChart').getContext('2d');

        // Generate dynamic colors based on percentage
        const maxData = Math.max(...data);
        const backgroundColors = data.map(value => {
            const percentage = (value / maxData) * 100;
            if (percentage >= 80) return 'rgba(40, 167, 69, 0.7)';      // Green for high
            if (percentage >= 50) return 'rgba(255, 193, 7, 0.7)';     // Yellow for medium
            return 'rgba(220, 53, 69, 0.7)';                          // Red for low
        });

        const borderColors = data.map(value => {
            const percentage = (value / maxData) * 100;
            if (percentage >= 80) return 'rgb(40, 167, 69)';
            if (percentage >= 50) return 'rgb(255, 193, 7)';
            return 'rgb(220, 53, 69)';
        });

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Booking',
                    data: data,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ctx.parsed.y + ' booking';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#fff' },
                        grid: { color: 'rgba(255, 255, 255, 0.1)' }
                    },
                    x: {
                        ticks: { color: '#fff' },
                        grid: { color: 'rgba(255, 255, 255, 0.1)' }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
