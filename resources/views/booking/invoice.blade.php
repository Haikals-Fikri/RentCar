<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Invoice {{ $booking->id }}</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');

    body {
        font-family: 'Inter', sans-serif;
        background-color: #f5f6fa;
        margin: 0;
        padding: 0;
        color: #2d2d2d;
    }

    .header {
        background-color: #111827;
        color: #fff;
        text-align: center;
        padding: 25px 0;
        border-bottom: 4px solid #ffd700;
    }

    .header h1 {
        margin: 0;
        font-size: 22px;
        font-weight: 600;
    }

    .container {
        background-color: #fff;
        max-width: 600px;
        margin: 30px auto;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }

    h2 {
        font-size: 18px;
        margin-top: 0;
        color: #1f2937;
        text-align: center;
    }

    p {
        font-size: 14px;
        line-height: 1.7;
        margin: 6px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 14px;
    }

    th {
        background: #fff9e6;
        color: #333;
        padding: 10px;
        text-align: left;
        border-bottom: 2px solid #ffd700;
    }

    td {
        padding: 10px;
        border-bottom: 1px solid #f0f0f0;
    }

    .highlight {
        background-color: #fef3c7;
        border: 1px solid #facc15;
        padding: 14px;
        text-align: center;
        border-radius: 10px;
        font-weight: 600;
        color: #92400e;
        margin-top: 20px;
        font-size: 15px;
    }

    .note {
        background: #f9fafb;
        border-left: 4px solid #ffd700;
        padding: 12px 16px;
        font-size: 13px;
        margin-top: 20px;
        color: #555;
        border-radius: 6px;
    }

    .footer {
        text-align: center;
        font-size: 12px;
        color: #777;
        margin: 25px auto;
    }

    .footer span {
        color: #b58900;
        font-weight: 500;
    }
  </style>
</head>
<body>
  <div class="header">
    <h1>RENTCAR.ID</h1>
  </div>

  <div class="container">
    <h2>Hai, {{ Auth::user()->name }}</h2>
    <p>Terima kasih sudah melakukan pemesanan kendaraan di <strong>RentCar.id</strong>.Berikut detail transaksimu pada  <strong>{{ $booking->vehicle->owner->ownerProfile->business_name ?? 'Rental Mobil' }}</p></strong>

    <table>
      <tr>
        <th>Keterangan</th>
        <th>Detail</th>
      </tr>
      <tr>
        <td>Kendaraan</td>
        <td>{{ $booking->vehicle->name }}</td>
      </tr>
      <tr>
        <td>Tanggal Booking</td>
        <td>{{ $booking->created_at->format('d F Y H:i') }}</td>
      </tr>
      <tr>
        <td>Status</td>
        <td>{{ ucfirst($booking->status) }}</td>
      </tr>
      <tr>
        <td>Total Pembayaran</td>
        <td>Rp {{ number_format($booking->total_payment, 0, ',', '.') }}</td>
      </tr>
    </table>

    <div class="highlight">
      Total yang harus dibayar: Rp {{ number_format($booking->total_payment, 0, ',', '.') }}
    </div>

    <div class="note">
      Segera lakukan pembayaran agar pemesananmu bisa diproses.
      Jika ada kendala, hubungi admin lewat halaman kontak di website.
    </div>
  </div>

  <div class="footer">
    <p>Terima kasih sudah memilih <span>RentCar.id</span> 🚗</p>
    <p>Invoice ini dibuat otomatis oleh sistem kami.</p>
  </div>
</body>
</html>
