<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Gabung Mitra | RentCar</title>
    <style>
        body {
            background: #111;
            color: #f0f0f0;
            font-family: 'Inter', sans-serif;
            margin: 0;
        }
        .container {
            max-width: 1140px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .banner {
            position: relative;
            background: url('images/mobil-banner.png') no-repeat center center/cover;
            height: 300px;
            border-radius: 16px;
            overflow: hidden;
        }
        .banner-content {
            position: absolute;
            top: 0;
            left: 0;
            padding: 40px;
        }
        .banner h1 {
            font-size: 36px;
            margin-bottom: 16px;
        }
        .banner h1 span {
            color: #FFD700;
        }
        .banner p {
            max-width: 500px;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .btn {
            background: #FFD700;
            color: #111;
            padding: 14px 28px;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
        }
        .section-title {
            font-size: 28px;
            margin: 50px 0 20px;
        }
        .benefits {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 20px;
        }
        .benefit-box {
            background: #1c1c1c;
            padding: 20px;
            border-radius: 14px;
            text-align: center;
        }
        .benefit-box h4 {
            margin: 14px 0 8px;
            font-size: 18px;
        }
        .benefit-box p {
            font-size: 14px;
            color: #ccc;
        }
        .testimoni {
            background: #1c1c1c;
            padding: 20px;
            border-radius: 14px;
            margin-top: 50px;
        }
        .testimoni p {
            font-size: 14px;
            color: #ccc;
        }
        .testimoni strong {
            display: block;
            margin-top: 12px;
            color: #FFD700;
        }
        .lihat-semua {
            display: inline-block;
            margin-top: 20px;
            background: #FFD700;
            color: #111;
            padding: 12px 24px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="container">

    <div class="banner">
        <div class="banner-content">
            <h1>Ayo bergabung dalam <span>kemitraan</span></h1>
            <p>Apapun tujuan Anda, kami menyediakan beragam pilihan mobil yang siap menemani perjalanan, dari mobilitas harian di tengah kota hingga petualangan seru ke luar kota.</p>
            <a href="#" class="btn">Daftar Mitra</a>
        </div>
    </div>

    <h2 class="section-title">Keuntungan Bergabung Dalam Kemitraan</h2>

    <div class="benefits">
        <div class="benefit-box">
            <img src="images/icon-laporan.png" alt="" width="40">
            <h4>Laporan Keuangan Transparansi</h4>
            <p>Dapatkan laporan keuangan dan informasi mengenai rental kamu.</p>
        </div>
        <div class="benefit-box">
            <img src="images/icon-pasar.png" alt="" width="40">
            <h4>Akses Pasar Lebih Luas</h4>
            <p>Owner rental tidak perlu mencari pelanggan sendiri.</p>
        </div>
        <div class="benefit-box">
            <img src="images/icon-admin.png" alt="" width="40">
            <h4>Kemudahan Administrasi</h4>
            <p>Platform menyediakan invoice dan bukti transaksi otomatis.</p>
        </div>
        <div class="benefit-box">
            <img src="images/icon-pesan.png" alt="" width="40">
            <h4>Pemesanan Prioritas</h4>
            <p>Pemesanan langsung lewat sistem, data masuk ke dashboard owner.</p>
        </div>
    </div>

    <div class="testimoni">
        <h3 style="margin-bottom:16px;">Beberapa Ulasan Dari Mitra Yang Bergabung</h3>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed eu aliquam magna. Pellentesque habitant morbi tristique senectus et netus.</p>
        <strong>— Owner 1</strong>
        <a href="#" class="lihat-semua">Lihat Semua →</a>
    </div>

</div>

</body>
</html>
