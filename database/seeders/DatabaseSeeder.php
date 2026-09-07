<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Users
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $users = User::factory(5)->create();
        $allUsers = $users->push($admin);

        // Workflow Stages
        $stages = [
            ['name' => 'Ideation', 'slug' => 'ideation', 'order' => 1, 'auto_advance_threshold' => 10, 'color' => '#3b82f6', 'icon' => 'lightbulb'],
            ['name' => 'Peer Voting', 'slug' => 'peer-voting', 'order' => 2, 'auto_advance_threshold' => 50, 'color' => '#8b5cf6', 'icon' => 'users'],
            ['name' => 'Expert Review', 'slug' => 'expert-review', 'order' => 3, 'auto_advance_threshold' => null, 'color' => '#f59e0b', 'icon' => 'shield-check'],
            ['name' => 'Incubation', 'slug' => 'incubation', 'order' => 4, 'auto_advance_threshold' => null, 'color' => '#10b981', 'icon' => 'rocket'],
            ['name' => 'Launch', 'slug' => 'launch', 'order' => 5, 'auto_advance_threshold' => null, 'color' => '#ef4444', 'icon' => 'flag'],
        ];
        DB::table('workflow_stages')->insert(array_map(function($stage) {
            $stage['created_at'] = Carbon::now();
            $stage['updated_at'] = Carbon::now();
            return $stage;
        }, $stages));

        // Badges
        $badges = [
            ['name' => 'Inovator Pemula', 'slug' => 'inovator-pemula', 'description' => 'Mempublikasikan 1 ide', 'criteria_type' => 'ideas_count', 'criteria_threshold' => 1],
            ['name' => 'Inovator Bintang', 'slug' => 'inovator-bintang', 'description' => 'Mempublikasikan 5 ide', 'criteria_type' => 'ideas_count', 'criteria_threshold' => 5],
            ['name' => 'Kontributor Aktif', 'slug' => 'kontributor-aktif', 'description' => 'Memberikan 10 komentar', 'criteria_type' => 'comments_count', 'criteria_threshold' => 10],
            ['name' => 'Mentor', 'slug' => 'mentor', 'description' => 'Memberikan 100 komentar', 'criteria_type' => 'comments_count', 'criteria_threshold' => 100],
            ['name' => 'Voter Setia', 'slug' => 'voter-setia', 'description' => 'Memberikan 50 vote', 'criteria_type' => 'votes_given', 'criteria_threshold' => 50],
        ];
        DB::table('badges')->insert(array_map(function($badge) {
            $badge['created_at'] = Carbon::now();
            $badge['updated_at'] = Carbon::now();
            return $badge;
        }, $badges));

        // Ideas
        // Ideas with rich realistic Indonesian innovation topics
        $realisticIdeas = [
            [
                'title' => 'SmartBin IoT: Pemilah Sampah Kampus Otomatis Berbasis AI Vision',
                'description' => 'Sistem tempat sampah pintar menggunakan Computer Vision dan sensor ultrasonik untuk mendeteksi dan memisahkan sampah organik, anorganik, dan B3 secara otomatis. Dilengkapi dashboard analitik volume sampah untuk petugas kebersihan kampus.',
                'category' => 'teknologi',
                'current_stage' => 'peer-voting',
                'tags' => ['iot', 'ai', 'smart-campus', 'green-tech'],
                'upvotes_count' => 64,
                'downvotes_count' => 3,
                'comments_count' => 14,
            ],
            [
                'title' => 'PeerTutor: Platform Crowdsourced Belajar Bareng & Mentoring Antar Mahasiswa',
                'description' => 'Aplikasi penghubung mahasiswa yang membutuhkan bimbingan mata kuliah sulit dengan mahasiswa berprestasi yang bersedia menjadi tutor sebaya. Menggunakan sistem reward poin reputasi dan lencana akademik.',
                'category' => 'pendidikan',
                'current_stage' => 'expert-review',
                'tags' => ['edutech', 'peer-learning', 'mentoring', 'akademik'],
                'upvotes_count' => 88,
                'downvotes_count' => 4,
                'comments_count' => 22,
            ],
            [
                'title' => 'DaurUang: Bank Sampah Digital Terintegrasi E-Wallet Komunitas',
                'description' => 'Platform pengelolaan limbah daur ulang komunitas yang menukar botol plastik dan kertas dengan poin e-wallet atau voucher kantin kampus. Mendukung sirkular ekonomi lokal.',
                'category' => 'sosial',
                'current_stage' => 'incubation',
                'tags' => ['sirkular-ekonomi', 'fintech', 'social-impact', 'lingkungan'],
                'upvotes_count' => 112,
                'downvotes_count' => 6,
                'comments_count' => 31,
            ],
            [
                'title' => 'EduVR: Simulasi Laboratorium Kimia & Fisika Realitas Virtual Murah',
                'description' => 'Solusi praktikum laboratorium berbasis Virtual Reality untuk sekolah dan kampus dengan fasilitas terbatas. Menghindari bahaya zat kimia berbahaya dan mengurangi biaya bahan habis pakai praktikum.',
                'category' => 'pendidikan',
                'current_stage' => 'launch',
                'tags' => ['vr', 'edutech', 'stem', 'laboratorium'],
                'upvotes_count' => 145,
                'downvotes_count' => 8,
                'comments_count' => 45,
            ],
            [
                'title' => 'AgriSensor: Sistem Pemantauan Kesuburan Tanah & Irigasi Otomatis Petani Lokal',
                'description' => 'Perangkat sensor NPK tanah berbiaya murah yang terhubung ke aplikasi smartphone untuk memberikan rekomendasi pemupukan presisi dan jadwal irigasi otomatis bagi petani holtikultura.',
                'category' => 'teknologi',
                'current_stage' => 'ideation',
                'tags' => ['agritech', 'iot', 'smart-farming', 'hardware'],
                'upvotes_count' => 24,
                'downvotes_count' => 1,
                'comments_count' => 7,
            ],
            [
                'title' => 'KantinQ: Sistem Pemesanan Makanan Pre-Order Kantin Bebas Antre',
                'description' => 'Aplikasi pemesanan makanan kantin kampus dengan estimasi waktu masak real-time, pembayaran QRIS terintegrasi, dan fitur food-waste reduction diskon makanan akhir hari.',
                'category' => 'bisnis',
                'current_stage' => 'peer-voting',
                'tags' => ['foodtech', 'qris', 'kantin', 'startup'],
                'upvotes_count' => 42,
                'downvotes_count' => 2,
                'comments_count' => 11,
            ],
            [
                'title' => 'CurhatSahabat: AI Chatbot Pendengar & Screening Kesehatan Mental Mahasiswa',
                'description' => 'Layanan konseling anonim 24/7 didukung AI yang dilatih dengan prinsip psikologi positif, terintegrasi rujukan otomatis ke psikolog atau konselor resmi kampus bila terdeteksi indikasi krisis.',
                'category' => 'sosial',
                'current_stage' => 'ideation',
                'tags' => ['mental-health', 'ai-chatbot', 'kesehatan', 'konseling'],
                'upvotes_count' => 76,
                'downvotes_count' => 5,
                'comments_count' => 18,
            ],
            [
                'title' => 'ParkirPintar: Deteksi Slot Parkir Kampus Realtime via CCTV Kamera AI',
                'description' => 'Memanfaatkan kamera CCTV eksisting untuk mendeteksi slot parkir mobil & motor yang kosong tanpa perlu memasang sensor magnetik mahal di setiap slot. Mahasiswa dapat melihat denah parkir kosong sebelum tiba di kampus.',
                'category' => 'teknologi',
                'current_stage' => 'expert-review',
                'tags' => ['smart-parking', 'computer-vision', 'kampus', 'ai'],
                'upvotes_count' => 95,
                'downvotes_count' => 3,
                'comments_count' => 26,
            ],
        ];

        $ideas = [];
        foreach ($realisticIdeas as $idx => $rIdea) {
            $ideas[] = [
                'user_id' => $allUsers->random()->id,
                'title' => $rIdea['title'],
                'description' => $rIdea['description'],
                'category' => $rIdea['category'],
                'status' => 'active',
                'current_stage' => $rIdea['current_stage'],
                'is_published' => true,
                'tags' => json_encode($rIdea['tags']),
                'upvotes_count' => $rIdea['upvotes_count'],
                'downvotes_count' => $rIdea['downvotes_count'],
                'comments_count' => $rIdea['comments_count'],
                'created_at' => Carbon::now()->subDays(rand(1, 20))->subHours(rand(1, 23)),
                'updated_at' => Carbon::now(),
            ];
        }
        DB::table('ideas')->insert($ideas);
        
        $ideaIds = DB::table('ideas')->pluck('id')->toArray();

        // Sample Comments
        $sampleComments = [
            'Ide yang sangat luar biasa! Menurut saya ini bisa langsung diujicobakan di lingkungan gedung fakultas.',
            'Keren banget solusinya. Apakah sudah mempertimbangkan integrasi dengan API sistem akademik kampus?',
            'Sangat setuju dengan konsep ini. Saya tertarik untuk bergabung sebagai desainer UI/UX bila tim butuh anggota!',
            'Bagus sekali! Saran saya tambahkan proteksi enkripsi untuk data privasi pengguna.',
            'Solusi tepat guna yang menjawab kebutuhan nyata mahasiswa saat ini. Upvoted!',
        ];

        foreach ($ideaIds as $iid) {
            for ($k = 0; $k < rand(2, 4); $k++) {
                DB::table('comments')->insert([
                    'user_id' => $allUsers->random()->id,
                    'idea_id' => $iid,
                    'body' => $sampleComments[array_rand($sampleComments)],
                    'created_at' => Carbon::now()->subHours(rand(2, 48)),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        // Team Positions (Crowdsourced Skill Matching)
        $samplePositions = [
            ['title' => 'UI/UX Designer', 'description' => 'Mendesain antarmuka mobile app & user testing', 'skills' => ['Figma', 'User Research']],
            ['title' => 'Fullstack Developer', 'description' => 'Membangun backend Laravel & frontend Vue/Alpine', 'skills' => ['Laravel', 'PHP', 'Tailwind']],
            ['title' => 'Machine Learning Engineer', 'description' => 'Membangun model rekomendasi & AI filter', 'skills' => ['Python', 'TensorFlow']],
            ['title' => 'Business Development & Pitching', 'description' => 'Menyusun pitch deck & business model canvas', 'skills' => ['Business Plan', 'Pitching']],
        ];

        foreach (array_slice($ideaIds, 0, 4) as $idx => $iid) {
            $pos = $samplePositions[$idx % count($samplePositions)];
            DB::table('team_positions')->insert([
                'idea_id' => $iid,
                'title' => $pos['title'],
                'description' => $pos['description'],
                'skills_required' => json_encode($pos['skills']),
                'max_applicants' => 2,
                'is_open' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        // Award some badges to admin and users
        $badgeIds = DB::table('badges')->pluck('id')->toArray();
        if (!empty($badgeIds)) {
            DB::table('badge_user')->insert([
                'badge_id' => $badgeIds[0],
                'user_id' => $admin->id,
                'awarded_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
