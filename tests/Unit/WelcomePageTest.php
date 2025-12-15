<?php

namespace Tests\Unit;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class WelcomePageTest extends TestCase
{
    #[Test]
    public function halaman_welcome_dapat_diakses_dan_memiliki_struktur_dasar()
    {
        $response = $this->get('/');

        // Assert 1: Status code OK
        $response->assertStatus(200);

        // Assert 2: Memiliki title yang benar (cari dalam tag title)
        $response->assertSee('<title>RentCar - Premium Car Rental</title>', false);

        // Assert 3: Memiliki navbar dengan logo (cari teks saja)
        $response->assertSeeText('RENTCAR');

        // Assert 4: Memiliki section hero (cari bagian teks hero)
        $response->assertSeeText('Awali perjalanan baru Anda');

        // Assert 5: Memiliki footer)
        $response->assertSeeText(' 2024 RentCar');
    }

    #[Test]
    public function navigasi_memiliki_semua_menu_utama_dan_login_options()
    {
        $response = $this->get('/');

        // Assert 1: Menu utama ada (cari teksnya saja)
        $response->assertSeeText('Beranda');
        $response->assertSeeText('Layanan');

        // Assert 2: Link WhatsApp kontak
        $response->assertSee('https://wa.me/6289504517110');

        // Assert 3: Login button ada
        $response->assertSeeText('Login');

        // Assert 4: Pilihan login user (cari link)
        $response->assertSee('href="/login-user"', false);

        // Assert 5: Pilihan login owner (cari link)
        $response->assertSee('href="/login-owner"', false);
    }

    #[Test]
    public function section_hero_menampilkan_konten_dan_cta_dengan_benar()
    {
        $response = $this->get('/');

        // Assert 1: Judul hero
        $response->assertSeeText('Awali perjalanan baru Anda');

        // Assert 2: Deskripsi hero
        $response->assertSeeText('Apapun tujuan Anda');

        // Assert 3: Tombol utama untuk user
        $response->assertSeeText('Mulai Sewa Mobil');
        $response->assertSee('href="/login-user"', false);

        // Assert 4: Tombol secondary untuk owner
        $response->assertSeeText('Login Mitra');
        $response->assertSee('href="/login-owner"', false);

        // Assert 5: Ada gambar mobil
        $response->assertSee('mobil3.png', false);
    }

    #[Test]
    public function menampilkan_fitur_dan_layanan_utama()
    {
        $response = $this->get('/');

        // Assert 1: Section benefits ada
        $response->assertSeeText('Kenapa Sewa di RentCar?');

        // Assert 2: Fitur untuk pelanggan ada
        $response->assertSeeText('Armada Premium');
        $response->assertSeeText('Pembayaran Fleksibel');

        // Assert 3: Testimoni ada
        $response->assertSeeText('Apa Kata Mereka?');
        $response->assertSee('Rian', false);

        // Assert 4: Slider mobil ada
        $response->assertSeeText('Pilihan Mobil Favorit');
        $response->assertSeeText('Honda Brio');

        // Assert 5: Button CTA ada
        $response->assertSeeText('Sewa Mobil Sekarang');
        $response->assertSeeText('Lihat Semua Mobil');
    }

    #[Test]
    public function menampilkan_informasi_kemitraan_dan_kontak()
    {
        $response = $this->get('/');

        // Assert 1: Section kemitraan ada
        $response->assertSeeText('Keuntungan Bergabung');

        // Assert 2: Benefit kemitraan ada
        $response->assertSeeText('Laporan Transparansi');
        $response->assertSeeText('Akses Pasar');

        // Assert 3: Tombol kemitraan ada
        $response->assertSeeText('Daftar Mitra');
        $response->assertSeeText('Login Mitra');

        // Assert 4: WhatsApp button ada
        $response->assertSee('wa-float', false);
        $response->assertSee('wa.me/6289504517110', false);

        // Assert 5: Informasi kontak di footer
        $response->assertSeeText('+62');
        $response->assertSeeText('@rentcar.co.id', false);
    }
}
