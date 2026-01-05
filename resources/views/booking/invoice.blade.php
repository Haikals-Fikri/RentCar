<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $booking->id }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.5;
            font-size: 12px; /* Ukuran standar dokumen profesional */
        }
        .header {
            background-color: #111827;
            color: #ffd700;
            text-align: center;
            padding: 25px 0;
            border-bottom: 4px solid #ffd700;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .container {
            padding: 30px 45px;
        }
        .invoice-info {
            width: 100%;
            margin-bottom: 25px;
        }
        h2 {
            font-size: 16px;
            color: #111827;
            margin: 0 0 8px 0;
            text-transform: uppercase;
        }
        p { margin: 3px 0; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background-color: #f8f9fa;
            color: #111827;
            padding: 10px 12px;
            text-align: left;
            border-bottom: 2px solid #ffd700;
            font-size: 11px;
            text-transform: uppercase;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #eeeeee;
            font-size: 12px;
        }
        .payment-box {
            background-color: #fffbeb;
            border: 1px solid #fef3c7;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
        }
        .payment-box h3 {
            margin: 0 0 10px 0;
            font-size: 15px;
            color: #92400e;
        }
        .total-amount {
            font-size: 20px;
            font-weight: bold;
            color: #111827;
            display: block;
            margin-bottom: 10px;
        }
        .status-stamp {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
            margin-bottom: 10px;
        }
        .status-paid { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .status-pending { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }

        .bank-details {
            margin-top: 10px;
            padding-top: 12px;
            border-top: 1px dashed #facc15;
            font-size: 11.5px;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eeeeee;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>RentCar.id</h1>
    </div>

    <div class="container">
        <table class="invoice-info">
            <tr>
                <td style="width: 55%; border:none;">
                    <h2>Invoice To:</h2>
                    <p><strong>{{ $booking->name }}</strong></p>
                    <p>{{ $booking->phone }}</p>
                    <p>{{ $booking->address }}</p>
                </td>
                <td style="width: 45%; text-align: right; border:none;">
                    <p><strong>ID Transaksi:</strong> #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</p>
                    <p><strong>Tanggal Pesan:</strong> {{ $booking->created_at->format('d/m/Y') }}</p>
                    <p><strong>Metode Bayar:</strong> {{ $booking->payment_method }}</p>
                </td>
            </tr>
        </table>

        <p style="font-size: 12px;">Mitra Penyedia: <strong>{{ $booking->vehicle->owner->ownerProfile->business_name ?? 'Mitra RentCar' }}</strong></p>

        <table>
            <thead>
                <tr>
                    <th>Deskripsi Kendaraan</th>
                    <th style="text-align: right;">Periode Sewa</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $booking->vehicle->brand }} {{ $booking->vehicle->type }}</strong><br>
                        <span style="font-size: 11px; color: #666;">Nama Unit: {{ $booking->vehicle->name }}</span>
                    </td>
                    <td style="text-align: right;">
                        {{ \Carbon\Carbon::parse($booking->start_date)->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse($booking->end_date)->format('d M Y') }}
                    </td>
                </tr>
                <tr>
                    <td><strong>Status Pemesanan</strong></td>
                    <td style="text-align: right;"><strong>{{ strtoupper($booking->status) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="payment-box">
            <h3>Ringkasan Pembayaran</h3>

            @if(in_array($booking->status, ['Disetujui', 'Completed']))
                <div class="status-stamp status-paid">LUNAS / PAID</div>
            @else
                <div class="status-stamp status-pending">MENUNGGU PEMBAYARAN</div>
            @endif

            <span style="color: #6b7280; font-size: 11px; display: block;">Total yang harus dibayar:</span>
            <span class="total-amount">Rp {{ number_format($booking->total_payment, 0, ',', '.') }}</span>

            @if(in_array($booking->status, ['Disetujui', 'Completed']))
                <div class="bank-details" style="border-top: 1px solid #bbf7d0;">
                    <p>Pembayaran Anda telah berhasil dikonfirmasi. Terima kasih atas kepercayaan Anda.</p>
                </div>
            @elseif($booking->payment_method === 'Transfer Bank')
                <div class="bank-details">
                    <p><strong>Instruksi Transfer:</strong></p>
                    @if($ownerProfile && $ownerProfile->bank_account)
                        <p>No. Rekening: <strong>{{ $ownerProfile->bank_account }}</strong></p>
                        <p>Atas Nama: <strong>{{ $ownerProfile->owner_name }}</strong></p>
                    @else
                        <p><em>Silakan hubungi mitra untuk detail pembayaran.</em></p>
                    @endif
                    <p style="font-size: 10px; color: #92400e; margin-top: 5px;">*Harap unggah bukti transfer di menu Riwayat Booking.</p>
                </div>
            @else
                <div class="bank-details">
                    <p><strong>Instruksi Bayar Tunai:</strong></p>
                    <p>Pembayaran dilakukan saat serah terima unit di lokasi mitra.</p>
                </div>
            @endif
        </div>

        <div class="footer">
            <p>Invoice ini sah dan diterbitkan secara otomatis oleh sistem RentCar.id.<br>
            Jl. Contoh Alamat No. 123, Indonesia | support@rentcar.id</p>
        </div>
    </div>
</body>
</html>
