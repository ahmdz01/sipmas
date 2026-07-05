<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplaintFactory extends Factory
{
    protected $model = Complaint::class;

    // Koordinat sekitar Kabupaten Bandung
    private array $lokasiKabBandung = [
        ['name' => 'Jl. Raya Soreang, Soreang',           'lat' => -7.0242, 'lng' => 107.5190],
        ['name' => 'Pasar Banjaran, Banjaran',             'lat' => -7.0508, 'lng' => 107.5644],
        ['name' => 'Jl. Raya Cimareme, Ngamprah',         'lat' => -6.8822, 'lng' => 107.5041],
        ['name' => 'Alun-alun Baleendah',                  'lat' => -7.0028, 'lng' => 107.6247],
        ['name' => 'Jl. Raya Majalaya, Majalaya',         'lat' => -7.0453, 'lng' => 107.7525],
        ['name' => 'Pertigaan Katapang, Katapang',         'lat' => -7.0031, 'lng' => 107.5642],
        ['name' => 'Jl. Terusan Kopo, Margahayu',         'lat' => -6.9631, 'lng' => 107.5886],
        ['name' => 'Pasar Ciparay, Ciparay',               'lat' => -7.0161, 'lng' => 107.6861],
        ['name' => 'Jl. Raya Cicalengka, Cicalengka',     'lat' => -6.9992, 'lng' => 107.8361],
        ['name' => 'Taman Kopo Indah, Margaasih',         'lat' => -6.9711, 'lng' => 107.5522],
        ['name' => 'Jl. Raya Dayeuhkolot, Dayeuhkolot',   'lat' => -6.9958, 'lng' => 107.6306],
        ['name' => 'Jl. Bojongsoang, Bojongsoang',        'lat' => -6.9908, 'lng' => 107.6508],
    ];

    private array $templateKeluhan = [
        1 => [ // Infrastruktur
            ['title' => 'Jalan Berlubang Berbahaya', 'desc' => 'Terdapat lubang besar di tengah jalan dengan diameter sekitar 50cm dan kedalaman 20cm. Sudah beberapa kali menyebabkan pengendara motor terjatuh, terutama saat malam hari karena tidak ada penerangan.'],
            ['title' => 'Jembatan Retak dan Rapuh', 'desc' => 'Kondisi jembatan penghubung antar desa sudah sangat memprihatinkan. Terdapat retakan besar di bagian tengah dan beberapa papan kayu yang sudah lapuk. Warga khawatir jembatan akan ambruk.'],
            ['title' => 'Drainase Tersumbat Menyebabkan Banjir', 'desc' => 'Saluran drainase di sepanjang jalan sudah tersumbat sampah dan lumpur. Setiap hujan deras, air meluap ke jalan dan masuk ke rumah warga sekitar.'],
            ['title' => 'Lampu Jalan Mati Sejak Sebulan Lalu', 'desc' => 'Lampu penerangan jalan di kawasan ini sudah mati lebih dari satu bulan. Warga merasa tidak aman saat beraktivitas malam hari, sudah ada laporan pencurian akibat kondisi gelap ini.'],
        ],
        2 => [ // Kebersihan
            ['title' => 'Tumpukan Sampah Tidak Diangkut Seminggu', 'desc' => 'Sampah di TPS depan pasar sudah menumpuk selama seminggu lebih dan belum diangkut. Bau busuk menyebar ke pemukiman warga dan dikhawatirkan menjadi sarang penyakit.'],
            ['title' => 'Pembuangan Sampah Liar di Pinggir Sungai', 'desc' => 'Terdapat oknum yang membuang sampah rumah tangga secara sembarangan di pinggir sungai Citarum. Sampah menumpuk dan mencemari aliran sungai yang digunakan warga untuk kebutuhan sehari-hari.'],
            ['title' => 'Selokan Penuh Sampah dan Berbau', 'desc' => 'Selokan di sepanjang Jalan ini dipenuhi sampah plastik, sehingga air tidak mengalir lancar. Bau tidak sedap mengganggu warga dan dikhawatirkan menjadi sarang nyamuk demam berdarah.'],
            ['title' => 'Fasilitas Tempat Sampah Rusak', 'desc' => 'Tempat sampah umum di taman sudah rusak dan tidak berfungsi. Akibatnya warga membuang sampah sembarangan di sekitar area tersebut sehingga terlihat kumuh.'],
        ],
        3 => [ // Ketertiban
            ['title' => 'Parkir Liar Menghalangi Arus Lalu Lintas', 'desc' => 'Banyak kendaraan yang parkir sembarangan di badan jalan depan pasar sehingga menyebabkan kemacetan parah terutama pada pagi dan sore hari. Jalan yang hanya dua lajur menjadi macet total.'],
            ['title' => 'Pedagang Kaki Lima Mengganggu Trotoar', 'desc' => 'PKL berjualan di atas trotoar sehingga pejalan kaki terpaksa berjalan di badan jalan. Kondisi ini sangat membahayakan keselamatan pejalan kaki, terutama anak-anak dan lansia.'],
            ['title' => 'Kebisingan Usaha Malam Hari Mengganggu Warga', 'desc' => 'Sebuah usaha karaoke di kawasan ini beroperasi hingga larut malam dan mengeluarkan suara yang sangat keras. Warga tidak bisa tidur dengan nyenyak dan sudah beberapa kali menegur namun tidak diindahkan.'],
            ['title' => 'Galian Kabel Tidak Dipasang Rambu Pengaman', 'desc' => 'Ada galian kabel PLN/Telkom di tengah jalan yang sudah 3 hari dibiarkan terbuka tanpa rambu peringatan dan pagar pengaman. Sangat berbahaya terutama di malam hari.'],
        ],
        4 => [ // Fasilitas Publik
            ['title' => 'Fasilitas MCK Umum Rusak dan Kotor', 'desc' => 'Toilet umum di dekat pasar kondisinya sangat memprihatinkan. Pintu rusak, lantai kotor berlumut, dan tidak ada air bersih mengalir. Warga tidak nyaman menggunakannya.'],
            ['title' => 'Taman Bermain Anak Tidak Terawat', 'desc' => 'Fasilitas taman bermain anak di RW ini sudah lama tidak mendapat perawatan. Beberapa wahana seperti ayunan dan perosotan dalam kondisi rusak dan berkarat, berbahaya untuk anak-anak.'],
            ['title' => 'Posyandu Kekurangan Alat Kesehatan', 'desc' => 'Posyandu di desa ini sudah lama tidak mendapat suplai alat kesehatan seperti timbangan bayi, tensimeter, dan obat-obatan dasar. Pelayanan kesehatan masyarakat menjadi terhambat.'],
            ['title' => 'Halte Bus Rusak Tidak Layak Pakai', 'desc' => 'Halte bus di jalan utama kondisinya sangat rusak. Atap bocor, bangku patah, dan dinding retak. Penumpang terpaksa kepanasan dan kehujanan saat menunggu kendaraan umum.'],
        ],
    ];

    public function definition(): array
    {
        $lokasi    = $this->faker->randomElement($this->lokasiKabBandung);
        $categoryId = $this->faker->numberBetween(1, 4);
        $template  = $this->faker->randomElement($this->templateKeluhan[$categoryId]);
        $userId    = User::where('role', 'masyarakat')->inRandomOrder()->value('id') ?? 2;
        $adminId   = User::where('role', 'admin')->inRandomOrder()->value('id') ?? 1;

        $status    = $this->faker->randomElement([
            'pending', 'pending',
            'verified',
            'in_progress',
            'resolved', 'resolved',
            'rejected',
        ]);

        $createdAt  = $this->faker->dateTimeBetween('-6 months', 'now');
        $verifiedAt = null;
        $resolvedAt = null;
        $handledBy  = null;
        $rejectionReason = null;

        if (in_array($status, ['verified', 'in_progress', 'resolved'])) {
            $verifiedAt = (clone $createdAt)->modify('+' . rand(1, 3) . ' days');
            $handledBy  = $adminId;
        }
        if ($status === 'resolved') {
            $resolvedAt = (clone $verifiedAt)->modify('+' . rand(3, 14) . ' days');
        }
        if ($status === 'rejected') {
            $handledBy = $adminId;
            $rejectionReason = $this->faker->randomElement([
                'Laporan tidak disertai bukti foto yang jelas.',
                'Lokasi yang dilaporkan berada di luar wilayah kewenangan dinas.',
                'Masalah yang dilaporkan sudah pernah ditangani sebelumnya.',
                'Informasi yang diberikan tidak lengkap dan tidak dapat diverifikasi.',
            ]);
        }

        static $counter = 0;
        $counter++;
        $year = date('Y', $createdAt->getTimestamp());
        $complaintNumber = 'SPM-' . $year . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);

        return [
            'complaint_number'  => $complaintNumber,
            'user_id'           => $userId,
            'category_id'       => $categoryId,
            'title'             => $template['title'],
            'description'       => $template['desc'],
            'location_name'     => $lokasi['name'],
            'latitude'          => $lokasi['lat'] + $this->faker->randomFloat(4, -0.005, 0.005),
            'longitude'         => $lokasi['lng'] + $this->faker->randomFloat(4, -0.005, 0.005),
            'photo'             => null,
            'status'            => $status,
            'rejection_reason'  => $rejectionReason,
            'handled_by'        => $handledBy,
            'verified_at'       => $verifiedAt,
            'resolved_at'       => $resolvedAt,
            'created_at'        => $createdAt,
            'updated_at'        => $resolvedAt ?? $verifiedAt ?? $createdAt,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn ($attr) => ['status' => 'pending', 'handled_by' => null, 'verified_at' => null, 'resolved_at' => null]);
    }

    public function resolved(): static
    {
        return $this->state(fn ($attr) => [
            'status'      => 'resolved',
            'verified_at' => now()->subDays(7),
            'resolved_at' => now()->subDays(2),
        ]);
    }
}