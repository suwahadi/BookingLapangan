<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Tentang Kami',
                'slug' => 'about',
                'content_html' => '<h3>Tentang Yomabar</h3><p>Yomabar adalah platform booking lapangan olahraga terdepan di Indonesia. Kami menghubungkan pemilik venue dengan ribuan atlet dan pecinta olahraga setiap harinya.</p><p>Misi kami adalah memudahkan akses olahraga bagi semua orang. Dengan teknologi yang handal, kami memastikan setiap jadwal terkelola dengan baik tanpa bentrok.</p><p>Sejak berdiri tahun 2024, kami telah melayani lebih dari 10.000 transaksi booking sukses.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Hubungi Kami',
                'slug' => 'contact',
                'content_html' => '<h3>Hubungi Tim Support</h3><p>Punya pertanyaan atau kendala? Jangan ragu untuk menghubungi tim support kami yang siap membantu 24/7.</p><ul><li>Email: support@yomabar.com</li><li>WhatsApp: +62 812-3456-7890</li><li>Alamat: Jl. Sudirman No. 1, Jakarta Pusat</li></ul>',
                'is_active' => true,
            ],
            [
                'title' => 'Syarat & Ketentuan',
                'slug' => 'terms-conditions',
                'content_html' => '<h3>Syarat & Ketentuan Penggunaan</h3><p>Selamat datang di Yomabar. Dengan mengakses platform ini, Anda menyetujui syarat dan ketentuan berikut:</p><ol><li>Pengguna wajib memberikan data diri yang valid.</li><li>Pembayaran yang sudah dilakukan tidak dapat ditarik kembali kecuali sesuai kebijakan refund venue.</li><li>Dilarang menggunakan platform untuk tindakan ilegal.</li></ol><p>Kami berhak memblokir akun yang melanggar ketentuan ini tanpa pemberitahuan sebelumnya.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Kebijakan Privasi',
                'slug' => 'privacy-policy',
                'content_html' => '<h3>Kebijakan Privasi Data</h3><p>Kami sangat menghargai privasi Anda. Data yang kami kumpulkan hanya digunakan untuk keperluan transaksi dan peningkatan layanan.</p><p>Kami tidak akan pernah menjual data pribadi Anda kepada pihak ketiga.</p><p>Data pembayaran diproses melalui gateway pembayaran yang aman dan terenkripsi.</p>',
                'is_active' => true,
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}
