<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Laporan Booking - {{ config('app.name', 'Rental Mobil') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #B8860B;
            margin: 0;
            font-size: 24px;
        }
        .summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .summary-item {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            background: #f9f9f9;
        }

        /* ---------- CHART ---------- */
        .chart-container {
            margin: 30px 0;
        }
        .chart-title {
            color: #B8860B;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .chart-wrapper {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            background: #fff;
        }
        /* batang container */
        .bars-wrap {
            display: table;
            width: 100%;
            table-layout: fixed;
            height: 200px;
            position: relative;
        }
        .bar-col {
            display: table-cell;
            vertical-align: bottom;
            text-align: center;
            padding: 0 4px;
        }
        .bar {
            width: 26px;
            margin: 0 auto;
            background: #ddd;
            position: relative;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .bar-val {
            position: absolute;
            top: -22px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 11px;
            font-weight: bold;
        }
        .bar-label {
            margin-top: 8px;
            font-size: 10px;
            font-weight: bold;
            white-space: nowrap;
        }

        /* ---------- TABLE ---------- */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px 12px;
            text-align: left;
        }
        th {
            background: #f8f9fa;
        }

        /* ---------- INSIGHT ---------- */
        .insights {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .insights h3 {
            color: #B8860B;
            margin-top: 0;
        }

        /* ---------- FOOTER ---------- */
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        /* ---------- PRINT ---------- */
        @media print {
            body {
                margin: 0;
                padding: 15px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .chart-container {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="header">
        <h1>LAPORAN BOOKING RENTAL MOBIL</h1>
        <p>
            Periode:
            @if($filter == 'mingguan') Minggu Ini
            @elseif($filter == 'bulanan') {{ $dropdownOptions['months'][$month] }} {{ $year }}
            @elseif($filter == 'tahunan') Tahun {{ $year }}
            @else Custom - {{ $date }}
            @endif
        </p>
        <p>Dicetak pada: {{ $printDate }}</p>
    </div>

    <!-- SUMMARY -->
    <div class="summary">
        <div class="summary-item">
            <div class="label">Total Booking</div>
            <div class="value">{{ $mainData['stats']['total'] }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Rata-rata</div>
            <div class="value">{{ $mainData['stats']['average'] }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Puncak</div>
            <div class="value">{{ $mainData['stats']['peak_label'] }} ({{ $mainData['stats']['peak'] }})</div>
        </div>
    </div>

    <!-- CHART -->
    <div class="chart-container">
        <h3 class="chart-title">Grafik Distribusi Booking</h3>
        <div class="chart-wrapper">
            @php
                $maxData  = max($mainData['data']) ?: 1;
                $maxHeight= 200;
            @endphp
            <div class="bars-wrap">
                @foreach($mainData['data'] as $index => $value)
                    @php
                        $height = ($value / $maxData) * $maxHeight;
                        $pct    = ($value / $maxData) * 100;
                        $color  = $pct >= 80 ? '#28a745' : ($pct >= 50 ? '#ffc107' : '#dc3545');
                    @endphp
                    <div class="bar-col">
                        <div class="bar" style="height: {{ max($height,3) }}px; background:{{ $color }};">
                            @if($value > 0)<span class="bar-val">{{ $value }}</span>@endif
                        </div>
                        <div class="bar-label">{{ $mainData['labels'][$index] ?? '' }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <h3 style="color: #B8860B; border-bottom: 1px solid #ddd; padding-bottom: 8px;">
        Daftar Booking ({{ count($mainData['table_data']) }} data)
    </h3>
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
                <td>{{ $booking['status'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">Tidak ada data booking untuk periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- INSIGHT -->
    <div class="insights">
        <h3>Analisis & Insight</h3>
        <ul>
            @foreach($insights as $insight)
                <li>{!! strip_tags($insight, '<strong>') !!}</li>
            @endforeach
        </ul>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Rental Mobil</p>
        <p>© {{ date('Y') }} {{ config('app.name', 'Rental Mobil') }} - All rights reserved</p>
    </div>

    <!-- AUTO PRINT (opsional) -->
    <script>
        window.onload = function () {
            setTimeout(() => window.print(), 800);
        };
    </script>
</body>
</html>
