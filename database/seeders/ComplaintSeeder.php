<?php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\ComplaintUpdate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        $data = [
            // --- PENDING ---
            [
                'status' => 'pending',
                'category_id' => 1,
                'title' => 'Jalan Berlubang di Depan SDN Soreang 1',
                'description' => 'Terdapat lubang besar di jalan depan SDN Soreang 1 dengan diameter ±60cm. Sudah menyebabkan 2 pengendara motor terjatuh minggu ini. Mohon segera diperbaiki sebelum korban bertambah.',
                'location_name' => 'Jl. Raya Soreang, Depan SDN Soreang 1',
                'latitude' => -7.0251, 'longitude' => 107.5183,
                'days_ago' => 2,
                'updates' => [],
            ],
            [
                'status' => 'pending',
                'category_id' => 2,
                'title' => 'Sampah Menumpuk di TPS Pasar Banjaran',
                'description' => 'Sampah di TPS belakang Pasar Banjaran sudah tidak diangkut selama 5 hari. Volume sampah sudah meluber ke jalan, menimbulkan bau busuk dan dikhawatirkan menjadi sarang tikus dan nyamuk.',
                'location_name' => 'TPS Pasar Banjaran, Banjaran',
                'latitude' => -7.0515, 'longitude' => 107.5651,
                'days_ago' => 1,
                'updates' => [],
            ],
            [
                'status' => 'pending',
                'category_id' => 3,
                'title' => 'Parkir Liar Depan Minimarket Katapang',
                'description' => 'Kendaraan parkir di badan jalan depan minimarket menyebabkan kemacetan parah setiap hari, terutama jam 07.00-09.00 dan 16.00-18.00. Jalan sudah sangat sempit dan rawan kecelakaan.',
                'location_name' => 'Jl. Raya Katapang, Depan Minimarket',
                'latitude' => -7.0038, 'longitude' => 107.5635,
                'days_ago' => 3,
                'updates' => [],
            ],
            [
                'status' => 'pending',
                'category_id' => 4,
                'title' => 'Lampu Taman Alun-alun Baleendah Rusak',
                'description' => 'Sebagian besar lampu penerangan di taman alun-alun Baleendah sudah padam selama 2 minggu. Area taman menjadi gelap dan tidak aman, warga enggan berkunjung terutama malam hari.',
                'location_name' => 'Alun-alun Baleendah, Baleendah',
                'latitude' => -7.0035, 'longitude' => 107.6241,
                'days_ago' => 4,
                'updates' => [],
            ],

            // --- VERIFIED ---
            [
                'status' => 'verified',
                'category_id' => 1,
                'title' => 'Drainase Tersumbat di Perumahan Griya Asri',
                'description' => 'Saluran drainase utama di Perumahan Griya Asri tersumbat total. Setiap hujan, air menggenang setinggi 30cm dan masuk ke garasi rumah warga. Kondisi ini sudah berlangsung 3 bulan.',
                'location_name' => 'Perumahan Griya Asri, Margahayu',
                'latitude' => -6.9625, 'longitude' => 107.5892,
                'days_ago' => 10,
                'updates' => [
                    ['status' => 'verified', 'note' => 'Laporan telah diverifikasi. Tim akan melakukan survei lapangan dalam 2 hari kerja.', 'days_ago' => 8],
                ],
            ],
            [
                'status' => 'verified',
                'category_id' => 2,
                'title' => 'Pembuangan Limbah Pabrik ke Sungai Citarum',
                'description' => 'Terdapat pabrik tekstil yang membuang limbah cair berwarna merah langsung ke sungai Citarum tanpa pengolahan. Air sungai berubah warna dan ikan-ikan mati. Warga sangat resah.',
                'location_name' => 'Sungai Citarum, Dayeuhkolot',
                'latitude' => -6.9965, 'longitude' => 107.6312,
                'days_ago' => 7,
                'updates' => [
                    ['status' => 'verified', 'note' => 'Laporan telah diverifikasi oleh tim lingkungan. Koordinasi dengan Dinas LH sedang dilakukan.', 'days_ago' => 5],
                ],
            ],

            // --- IN PROGRESS ---
            [
                'status' => 'in_progress',
                'category_id' => 1,
                'title' => 'Jembatan Retak di Desa Cangkuang',
                'description' => 'Jembatan penghubung Desa Cangkuang mengalami retakan serius di tiang penyangga. Kendaraan roda empat tidak berani melintas. Warga terpaksa memutar sejauh 5km untuk ke kota.',
                'location_name' => 'Jembatan Desa Cangkuang, Banjaran',
                'latitude' => -7.0478, 'longitude' => 107.5712,
                'days_ago' => 20,
                'updates' => [
                    ['status' => 'verified',    'note' => 'Laporan diverifikasi. Kondisi jembatan dikonfirmasi kritis.', 'days_ago' => 18],
                    ['status' => 'in_progress', 'note' => 'Tim Dinas PUPR sudah turun ke lapangan. Pengerjaan perbaikan dijadwalkan mulai Senin depan.', 'days_ago' => 14],
                ],
            ],
            [
                'status' => 'in_progress',
                'category_id' => 4,
                'title' => 'Toilet Umum Pasar Majalaya Tidak Berfungsi',
                'description' => 'Fasilitas MCK umum di Pasar Majalaya sudah tidak berfungsi lebih dari 1 bulan. Pipa air bocor, 3 dari 5 pintu rusak. Pedagang dan pengunjung pasar sangat terganggu.',
                'location_name' => 'Pasar Majalaya, Majalaya',
                'latitude' => -7.0461, 'longitude' => 107.7518,
                'days_ago' => 15,
                'updates' => [
                    ['status' => 'verified',    'note' => 'Pengaduan terverifikasi. Dinas Perdagangan dihubungi untuk tindak lanjut.', 'days_ago' => 13],
                    ['status' => 'in_progress', 'note' => 'Material perbaikan sudah dipesan. Pekerjaan renovasi toilet akan dimulai minggu ini.', 'days_ago' => 9],
                ],
            ],
            [
                'status' => 'in_progress',
                'category_id' => 3,
                'title' => 'PKL Memblokir Trotoar Jl. Raya Cimareme',
                'description' => 'Puluhan PKL berjualan di trotoar Jl. Raya Cimareme sehingga pejalan kaki tidak bisa lewat. Penyandang disabilitas dan ibu-ibu dengan stroller sangat kesulitan.',
                'location_name' => 'Jl. Raya Cimareme, Ngamprah',
                'latitude' => -6.8829, 'longitude' => 107.5048,
                'days_ago' => 12,
                'updates' => [
                    ['status' => 'verified',    'note' => 'Laporan diverifikasi oleh Satpol PP.', 'days_ago' => 10],
                    ['status' => 'in_progress', 'note' => 'Operasi penertiban dijadwalkan besok pukul 08.00 WIB bersama Satpol PP dan Dishub.', 'days_ago' => 6],
                ],
            ],

            // --- RESOLVED ---
            [
                'status' => 'resolved',
                'category_id' => 1,
                'title' => 'Lampu Jalan Mati di Jl. Bojongsoang',
                'description' => 'Penerangan jalan di sepanjang Jl. Bojongsoang mati total sejak 3 minggu lalu. Warga sangat takut keluar malam hari karena gelap gulita dan sudah ada laporan kejahatan.',
                'location_name' => 'Jl. Bojongsoang, Bojongsoang',
                'latitude' => -6.9913, 'longitude' => 107.6511,
                'days_ago' => 30,
                'updates' => [
                    ['status' => 'verified',    'note' => 'Laporan diverifikasi. Teridentifikasi 12 titik lampu mati sepanjang 2km.', 'days_ago' => 28],
                    ['status' => 'in_progress', 'note' => 'Tim PLN sudah dihubungi dan dijadwalkan perbaikan besok pagi.', 'days_ago' => 25],
                    ['status' => 'resolved',    'note' => 'Perbaikan selesai dilaksanakan. 12 lampu jalan sudah menyala kembali. Terima kasih atas laporan warga.', 'days_ago' => 22],
                ],
            ],
            [
                'status' => 'resolved',
                'category_id' => 2,
                'title' => 'Tumpukan Sampah Liar di Pinggir Sungai Cikapundung',
                'description' => 'Ada tumpukan sampah ilegal di pinggir Sungai Cikapundung dekat Jembatan Margaasih. Sampah berserakan hingga ke badan sungai dan mencemari air.',
                'location_name' => 'Pinggir Sungai Cikapundung, Margaasih',
                'latitude' => -6.9718, 'longitude' => 107.5528,
                'days_ago' => 45,
                'updates' => [
                    ['status' => 'verified',    'note' => 'Laporan valid. Koordinasi dengan Dinas Kebersihan untuk pembersihan.', 'days_ago' => 43],
                    ['status' => 'in_progress', 'note' => 'Tim kebersihan dijadwalkan turun besok dengan armada truk sampah.', 'days_ago' => 41],
                    ['status' => 'resolved',    'note' => 'Area sudah dibersihkan total. 3 truk sampah dikerahkan. Papan larangan buang sampah sudah dipasang.', 'days_ago' => 38],
                ],
            ],
            [
                'status' => 'resolved',
                'category_id' => 4,
                'title' => 'Taman Bermain Anak RW 05 Rusak',
                'description' => 'Wahana taman bermain anak di RW 05 Katapang sudah rusak parah. Ayunan patah, perosotan berkarat, dan area bermain tidak aman untuk anak-anak.',
                'location_name' => 'Taman RW 05, Katapang',
                'latitude' => -7.0025, 'longitude' => 107.5648,
                'days_ago' => 60,
                'updates' => [
                    ['status' => 'verified',    'note' => 'Survei lapangan telah dilakukan. Kondisi taman memang perlu perbaikan menyeluruh.', 'days_ago' => 57],
                    ['status' => 'in_progress', 'note' => 'Anggaran perbaikan disetujui. Kontraktor mulai bekerja hari ini.', 'days_ago' => 50],
                    ['status' => 'resolved',    'note' => 'Renovasi taman bermain selesai. Wahana baru sudah terpasang dan aman digunakan.', 'days_ago' => 40],
                ],
            ],

            // --- REJECTED ---
            [
                'status' => 'rejected',
                'category_id' => 3,
                'title' => 'Keributan di Warung Malam Depan Gang',
                'description' => 'Sering ada keributan dan mabuk-mabukan di warung depan gang setiap malam Minggu hingga subuh. Sangat mengganggu ketenangan warga.',
                'location_name' => 'Gang Mawar, Soreang',
                'latitude' => -7.0238, 'longitude' => 107.5196,
                'days_ago' => 14,
                'rejection_reason' => 'Penanganan ketertiban masyarakat jenis ini merupakan kewenangan kepolisian setempat (Polsek). Mohon melaporkan langsung ke Polsek Soreang atau melalui aplikasi Polri Super App.',
                'updates' => [
                    ['status' => 'rejected', 'note' => 'Laporan tidak dapat diproses. Kasus ini bukan kewenangan dinas. Harap hubungi Polsek setempat.', 'days_ago' => 12],
                ],
            ],
            [
                'status' => 'rejected',
                'category_id' => 2,
                'title' => 'Tetangga Tidak Mau Kerja Bakti',
                'description' => 'Tetangga saya tidak pernah ikut kerja bakti dan selalu membuang sampah sembarangan di depan rumah saya.',
                'location_name' => 'Jl. Anggrek No. 12, Banjaran',
                'latitude' => -7.0502, 'longitude' => 107.5638,
                'days_ago' => 20,
                'rejection_reason' => 'Laporan yang masuk merupakan permasalahan antar warga yang bersifat personal dan bukan merupakan pengaduan infrastruktur/fasilitas publik. Disarankan untuk menyelesaikan melalui musyawarah warga atau melalui RT/RW setempat.',
                'updates' => [
                    ['status' => 'rejected', 'note' => 'Laporan ditolak karena bukan termasuk kategori pengaduan fasilitas publik.', 'days_ago' => 18],
                ],
            ],
        ];

        foreach ($data as $item) {
            $user  = User::where('role', 'masyarakat')->inRandomOrder()->first();
            $now   = Carbon::now();

            $complaint = Complaint::create([
                'complaint_number' => Complaint::generateNumber(),
                'user_id'          => $user->id,
                'category_id'      => $item['category_id'],
                'title'            => $item['title'],
                'description'      => $item['description'],
                'location_name'    => $item['location_name'],
                'latitude'         => $item['latitude'],
                'longitude'        => $item['longitude'],
                'photo'            => null,
                'status'           => $item['status'],
                'rejection_reason' => $item['rejection_reason'] ?? null,
                'handled_by'       => in_array($item['status'], ['verified', 'in_progress', 'resolved', 'rejected']) ? $admin->id : null,
                'verified_at'      => in_array($item['status'], ['verified', 'in_progress', 'resolved']) ? $now->copy()->subDays($item['days_ago'] - 2) : null,
                'resolved_at'      => $item['status'] === 'resolved' ? $now->copy()->subDays(max(1, $item['days_ago'] - 8)) : null,
                'created_at'       => $now->copy()->subDays($item['days_ago']),
                'updated_at'       => $now->copy()->subDays(max(0, $item['days_ago'] - 3)),
            ]);

            foreach ($item['updates'] as $update) {
                ComplaintUpdate::create([
                    'complaint_id' => $complaint->id,
                    'user_id'      => $admin->id,
                    'status'       => $update['status'],
                    'note'         => $update['note'],
                    'created_at'   => $now->copy()->subDays($update['days_ago']),
                    'updated_at'   => $now->copy()->subDays($update['days_ago']),
                ]);
            }
        }
    }
}