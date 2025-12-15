<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Vehicle;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DetailBooking extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'bulanan');
        $date = $request->get('date');
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $compareMode = $request->get('compare_mode', 'bulanan');

        // Menggunakan Carbon untuk default periodB (aman dari 0 jika bulan=1)
        $defaultPeriodB = Carbon::now()->subMonth()->format('m');
        $periodA = $request->get('periodA', date('m'));
        $periodB = $request->get('periodB', $defaultPeriodB);

        $mainData = $this->getMainData($filter, $date, $month, $year);
        $dropdownOptions = $this->getDropdownOptions();
        $comparisonData = $this->getComparisonData($compareMode, $periodA, $periodB);

        $insights = $this->generateInsights($mainData['data'], $mainData['type'], $mainData['stats']);

        return view('layouts.owner.detail-booking', [
            'filter' => $filter,
            'date' => $date,
            'month' => $month,
            'year' => $year,
            'compareMode' => $compareMode,
            'periodA' => $periodA,
            'periodB' => $periodB,
            'mainData' => $mainData,
            'dropdownOptions' => $dropdownOptions,
            'comparisonData' => $comparisonData,
            'insights' => $insights,
        ]);
    }

    public function printReport(Request $request)
    {
        $filter = $request->get('filter', 'bulanan');
        $date = $request->get('date');
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $mainData = $this->getMainData($filter, $date, $month, $year);
        $dropdownOptions = $this->getDropdownOptions();
        $insights = $this->generateInsights($mainData['data'], $mainData['type'], $mainData['stats']);

            $pdf = PDF::loadView('layouts.owner.print-booking-report', [
            'filter' => $filter,
            'date' => $date,
            'month' => $month,
            'year' => $year,
            'mainData' => $mainData,
            'dropdownOptions' => $dropdownOptions,
            'printDate' => now()->format('d F Y H:i:s'),
            'insights' => $insights,
        ]);
        return $pdf->download('laporan-booking-' . now()->format('Y-m-d') . '.pdf');
    }

    private function getMainData($filter, $date, $month, $year)
    {
        $baseQuery = Booking::with(['vehicle', 'user'])
            ->where('status', '!=', 'dibatalkan');

        return match ($filter) {
            'mingguan' => $this->getWeeklyData($baseQuery),
            'bulanan' => $this->getMonthlyData($baseQuery, $month, $year),
            'tahunan' => $this->getYearlyData($baseQuery, $year),
            'custom' => $this->getCustomData($baseQuery, $date),
            default => $this->getMonthlyData($baseQuery, date('m'), date('Y')),
        };
    }

    private function getWeeklyData($baseQuery)
    {
        // Tetapkan lokasi ke Indonesia agar minggu dimulai dari Senin (Opsional, tergantung konfigurasi Carbon global)
        Carbon::setLocale('id');

        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);

        $dailyData = DB::table('bookings')
            // PostgreSQL: EXTRACT(DOW FROM start_date)
            ->select(DB::raw('EXTRACT(DOW FROM start_date) as day_num, COUNT(*) as count'))
            ->whereBetween('start_date', [$startOfWeek, $endOfWeek])
            ->where('status', '!=', 'dibatalkan')
            ->groupBy('day_num')
            ->orderBy('day_num')
            ->get();

        // DOW: 0=Minggu, 1=Senin, ..., 6=Sabtu
        $daysOrder = [1, 2, 3, 4, 5, 6, 0]; // Urutan Senin - Minggu
        $daysIndo = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $labels = [];
        $data = [];

        foreach ($daysOrder as $key => $dayNum) {
            $dayData = $dailyData->firstWhere('day_num', (float)$dayNum); // Cast ke float karena EXTRACT mengembalikan float
            $labels[] = $daysIndo[$key];
            $data[] = $dayData ? $dayData->count : 0;
        }

        $bookings = $baseQuery->whereBetween('start_date', [$startOfWeek, $endOfWeek])->get();
        $tableData = $this->generateRealTableData($bookings);

        return [
            'type' => 'mingguan',
            'labels' => $labels,
            'data' => $data,
            'table_data' => $tableData,
            'stats' => $this->calculateStats($data, $labels)
        ];
    }

    private function getMonthlyData($baseQuery, $month, $year)
    {
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

        $dailyData = DB::table('bookings')
            // PERBAIKAN UTAMA: PostgreSQL menggunakan EXTRACT(DAY FROM start_date)
            ->select(DB::raw('EXTRACT(DAY FROM start_date) as day, COUNT(*) as count'))
            ->whereMonth('start_date', $month)
            ->whereYear('start_date', $year)
            ->where('status', '!=', 'dibatalkan')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $labels = [];
        $data = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            // Gunakan (float)$day jika EXTRACT mengembalikan float
            $dayData = $dailyData->get((float)$day);
            $labels[] = "Tgl $day";
            $data[] = $dayData ? $dayData->count : 0;
        }

        $bookings = $baseQuery->whereMonth('start_date', $month)->whereYear('start_date', $year)->get();
        $tableData = $this->generateRealTableData($bookings);

        return [
            'type' => 'bulanan',
            'labels' => $labels,
            'data' => $data,
            'table_data' => $tableData,
            'stats' => $this->calculateStats($data, $labels)
        ];
    }

    private function getYearlyData($baseQuery, $year)
    {
        $monthlyData = DB::table('bookings')
            // Perbaikan: PostgreSQL menggunakan EXTRACT(MONTH FROM start_date)
            ->select(DB::raw('EXTRACT(MONTH FROM start_date) as month, COUNT(*) as count'))
            ->whereYear('start_date', $year)
            ->where('status', '!=', 'dibatalkan')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $labels = [];
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            // Gunakan (float)$m jika EXTRACT mengembalikan float
            $monthData = $monthlyData->get((float)$m);
            $labels[] = Carbon::create($year, $m)->translatedFormat('M');
            $data[] = $monthData ? $monthData->count : 0;
        }

        $bookings = $baseQuery->whereYear('start_date', $year)->get();
        $tableData = $this->generateRealTableData($bookings);

        return [
            'type' => 'tahunan',
            'labels' => $labels,
            'data' => $data,
            'table_data' => $tableData,
            'stats' => $this->calculateStats($data, $labels)
        ];
    }

    private function getCustomData($baseQuery, $date)
    {
        if (!$date) return $this->getEmptyData();

        $customDate = Carbon::parse($date);
        $startDate = $customDate->copy()->subDays(2)->startOfDay();
        $endDate = $customDate->copy()->addDays(2)->endOfDay();

        $periodData = DB::table('bookings')
            ->select(DB::raw('DATE(start_date) as date, COUNT(*) as count'))
            ->whereBetween('start_date', [$startDate, $endDate])
            ->where('status', '!=', 'dibatalkan')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $data = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            $labels[] = $current->format('d M');
            $data[] = $periodData->get($dateStr) ? $periodData->get($dateStr)->count : 0;
            $current->addDay();
        }

        $bookings = $baseQuery->whereDate('start_date', $customDate)->get();
        $tableData = $this->generateRealTableData($bookings);

        return [
            'type' => 'custom',
            'labels' => $labels,
            'data' => $data,
            'table_data' => $tableData,
            'stats' => $this->calculateStats($data, $labels)
        ];
    }

    // ... (Fungsi generateRealTableData, mapStatus, calculateStats, getComparisonData, getBookingCountByPeriod, getDropdownOptions, dan getEmptyData tidak diubah) ...

    private function generateRealTableData($bookings)
    {
        $tableData = [];
        foreach ($bookings as $booking) {
            $startDate = Carbon::parse($booking->start_date);
            $endDate = Carbon::parse($booking->end_date);
            $durasi = $startDate->diffInDays($endDate);

            $tableData[] = [
                'tanggal' => $startDate->format('d M Y'),
                'nama' => $booking->name,
                'mobil' => $booking->vehicle->merk . ' ' . $booking->vehicle->model,
                'durasi' => $durasi . ' Hari',
                'tipe' => $booking->vehicle->transmisi ?? 'Matic',
                'status' => $this->mapStatus($booking->status)
            ];
        }
        return $tableData;
    }

    private function mapStatus($status)
    {
        $map = [
            'selesai' => 'Selesai',
            'berjalan' => 'Berjalan',
            'dipesan' => 'Dipesan',
            'dibatalkan' => 'Dibatalkan',
            'pending' => 'Pending',
            'confirmed' => 'Terkonfirmasi',
            'completed' => 'Selesai'
        ];
        return $map[$status] ?? $status;
    }

    private function calculateStats($data, $labels)
    {
        if (empty($data)) return ['total'=>0,'average'=>0,'peak'=>0,'peak_label'=>'-'];
        $total = array_sum($data);
        $average = round($total / count($data), 1);
        $peak = max($data);
        $peakIndex = array_search($peak, $data);
        return [
            'total' => $total,
            'average' => $average,
            'peak' => $peak,
            'peak_label' => $labels[$peakIndex] ?? 'Unknown'
        ];
    }

    private function getComparisonData($mode, $periodA, $periodB)
    {
        $countA = $this->getBookingCountByPeriod($mode, $periodA);
        $countB = $this->getBookingCountByPeriod($mode, $periodB);

        // Menghindari pembagian nol
        if ($countB === 0) {
            $percentage = $countA > 0 ? 100 : 0;
        } else {
            // Rumus persentase: (A - B) / B * 100
            $percentage = (($countA - $countB) / $countB) * 100;
        }

        return [
            'count_a' => $countA,
            'count_b' => $countB,
            'percentage' => round($percentage, 1),
            'trend' => $percentage > 0 ? 'up' : ($percentage < 0 ? 'down' : 'neutral')
        ];
    }

    private function getBookingCountByPeriod($mode, $period)
    {
        $query = Booking::where('status', '!=', 'dibatalkan');
        return match ($mode) {
            'mingguan' => $query->whereRaw('EXTRACT(WEEK FROM start_date) = ?', [(int)$period])->count(),
            'bulanan' => $query->whereMonth('start_date', (int)$period)->count(),
            'tahunan' => $query->whereYear('start_date', (int)$period)->count(),
            default => 0,
        };
    }

    private function getDropdownOptions()
    {
        $years = Booking::select(DB::raw('EXTRACT(YEAR FROM start_date) as year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $key = str_pad($i, 2, '0', STR_PAD_LEFT);
            $months[$key] = Carbon::create(null, $i)->translatedFormat('F');
        }

        return [
            'years' => $years,
            'months' => $months
        ];
    }

    private function getEmptyData()
    {
        return [
            'type' => 'empty',
            'labels' => [],
            'data' => [],
            'table_data' => [],
            'stats' => ['total'=>0,'average'=>0,'peak'=>0,'peak_label'=>'-']
        ];
    }

    private function generateInsights($data, $type, $stats)
    {
        if ($stats['total'] === 0) {
            return ["Tidak ada data booking untuk periode ini."];
        }

        $insights = [];
        $total = $stats['total'];
        $average = $stats['average'];
        $peak = $stats['peak'];
        $peakLabel = $stats['peak_label'];

        if ($type === 'mingguan') {
            $insights[] = "Permintaan puncak pada <strong>{$peakLabel}</strong> dengan <strong>{$peak}</strong> booking.";
            $insights[] = "Rata-rata per hari: <strong>{$average}</strong> booking.";
            $firstHalf = array_sum(array_slice($data, 0, 3));
            $secondHalf = array_sum(array_slice($data, 3));
            $trend = $secondHalf > $firstHalf ? 'naik' : 'turun';
            $insights[] = "Tren: <strong>{$trend}</strong> pada paruh akhir minggu.";
            $insights[] = "Rekomendasi: tambah unit matic di akhir pekan.";
        } elseif ($type === 'bulanan') {
            $insights[] = "Hari puncak pada <strong>{$peakLabel}</strong> dengan <strong>{$peak}</strong> booking.";
            $insights[] = "Rata-rata per hari: <strong>{$average}</strong> booking.";
            $midMonth = floor(count($data)/2);
            $firstHalf = array_sum(array_slice($data, 0, $midMonth));
            $secondHalf = array_sum(array_slice($data, $midMonth));
            $trend = $secondHalf > $firstHalf ? 'naik' : 'turun';
            $insights[] = "Tren: <strong>{$trend}</strong> pada paruh akhir bulan.";
            $insights[] = "Rekomendasi: cek ketersediaan unit & persiapan sebelum hari puncak.";
        } elseif ($type === 'tahunan') {
            $insights[] = "Bulan puncak: <strong>{$peakLabel}</strong> dengan <strong>{$peak}</strong> booking.";
            $insights[] = "Rata-rata per bulan: <strong>{$average}</strong> booking.";
            $insights[] = "Total booking tahun ini: <strong>{$total}</strong>.";
            $insights[] = "Rekomendasi: pertimbangkan paket promosi di bulan sepi.";
        }

        return $insights;
    }
}
