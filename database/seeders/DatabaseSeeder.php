<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Destination;
use App\Models\SustainabilityIndicator;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Dedicated admin account (credentials come from .env)
        $adminPassword = (string) config('admin.password');

        if ($adminPassword === '') {
            $this->command->warn('ADMIN_PASSWORD is not set - skipping admin account creation.');
        } else {
            User::firstOrCreate(
                ['email' => config('admin.email')],
                [
                    'name' => 'EcoJourney Admin',
                    'password' => Hash::make($adminPassword),
                    'role' => 'admin',
                    'email_verified_at' => now(),
                ]
            );
        }

        // Categories
        $nature = Category::create([
            'name' => 'Nature',
            'slug' => 'nature',
            'description' => 'Destinasi alam dan konservasi.',
        ]);

        $culture = Category::create([
            'name' => 'Culture',
            'slug' => 'culture',
            'description' => 'Destinasi budaya dan pengalaman lokal.',
        ]);

        $adventure = Category::create([
            'name' => 'Adventure',
            'slug' => 'adventure',
            'description' => 'Destinasi untuk aktivitas petualangan.',
        ]);

        $eco = Category::create([
            'name' => 'Eco Tourism',
            'slug' => 'eco-tourism',
            'description' => 'Destinasi dengan fokus perjalanan berkelanjutan.',
        ]);

        // Destinations
        $wayKambas = Destination::create([
            'category_id' => $nature->id,
            'name' => 'Way Kambas',
            'slug' => 'way-kambas',
            'location' => 'Lampung, Indonesia',
            'region' => 'Sumatera',
            'description' => 'Kawasan wisata alam yang dapat menjadi bagian dari perjalanan berbasis konservasi dan pengalaman alam.',
            'image' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=1200&q=80',
            'latitude' => -4.9247,
            'longitude' => 105.8017,
            'sustainability_score' => 86,
        ]);

        $pahawang = Destination::create([
            'category_id' => $eco->id,
            'name' => 'Pulau Pahawang',
            'slug' => 'pulau-pahawang',
            'location' => 'Pesawaran, Lampung',
            'region' => 'Sumatera',
            'description' => 'Destinasi kepulauan dengan pengalaman bahari dan aktivitas yang dekat dengan lingkungan pesisir.',
            'image' => 'https://images.unsplash.com/photo-1559827260-dc66d52bef19?auto=format&fit=crop&w=1200&q=80',
            'latitude' => -5.6644,
            'longitude' => 105.2203,
            'sustainability_score' => 82,
        ]);

        $kiluan = Destination::create([
            'category_id' => $nature->id,
            'name' => 'Teluk Kiluan',
            'slug' => 'teluk-kiluan',
            'location' => 'Tanggamus, Lampung',
            'region' => 'Sumatera',
            'description' => 'Kawasan pesisir dengan panorama alam dan pengalaman perjalanan yang berorientasi pada alam.',
            'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80',
            'latitude' => -5.7833,
            'longitude' => 105.1667,
            'sustainability_score' => 79,
        ]);

        $siger = Destination::create([
            'category_id' => $culture->id,
            'name' => 'Menara Siger',
            'slug' => 'menara-siger',
            'location' => 'Bakauheni, Lampung',
            'region' => 'Sumatera',
            'description' => 'Ikon wisata Lampung yang menawarkan pengalaman visual dan pengenalan budaya daerah.',
            'image' => 'https://images.unsplash.com/photo-1555899434-94d1368aa7af?auto=format&fit=crop&w=1200&q=80',
            'latitude' => -5.8500,
            'longitude' => 105.7550,
            'sustainability_score' => 74,
        ]);

        $extra = [
            ['Raja Ampat', 'raja-ampat', 'Papua Barat Daya', 'Papua', 'Kepulauan karst dengan keanekaragaman laut tertinggi di dunia.', 'https://images.unsplash.com/photo-1559827260-dc66d52bef19?auto=format&fit=crop&w=1200&q=80', -1.0167, 130.5167, 95, $eco->id],
            ['Bali Ubud', 'bali-ubud', 'Gianyar, Bali', 'Bali & Nusa Tenggara', 'Pusat budaya Bali dengan sawah hijau dan komunitas kreatif.', 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=1200&q=80', -8.5069, 115.2625, 88, $culture->id],
            ['Borobudur', 'borobudur', 'Magelang, Jawa Tengah', 'Jawa', 'Candi Buddha terbesar di dunia dan situs warisan dunia.', 'https://images.unsplash.com/photo-1596402184320-417e7178b2cd?auto=format&fit=crop&w=1200&q=80', -7.6079, 110.2038, 90, $culture->id],
            ['Bromo Tengger', 'bromo-tengger', 'Probolinggo, Jawa Timur', 'Jawa', 'Kaldera aktif dengan lautan pasir dan sunrise ikonik.', 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=1200&q=80', -7.9425, 112.9530, 87, $adventure->id],
            ['Komodo Island', 'komodo-island', 'Labuan Bajo, NTT', 'Bali & Nusa Tenggara', 'Habitat komodo dengan pantai pink dan savana eksotis.', 'https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=1200&q=80', -8.5795, 119.4885, 93, $nature->id],
            ['Danau Toba', 'danau-toba', 'Samosir, Sumatera Utara', 'Sumatera', 'Danau vulkanik terbesar di Asia Tenggara dengan Pulau Samosir.', 'https://images.unsplash.com/photo-1439066615861-d1af74d74000?auto=format&fit=crop&w=1200&q=80', 2.6845, 98.8756, 85, $nature->id],
            ['Tanjung Puting', 'tanjung-puting', 'Kotawaringin Barat, Kalimantan Tengah', 'Kalimantan', 'Suaka orangutan dengan susur sungai klotok ikonik.', 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=1200&q=80', -2.9350, 111.9500, 94, $eco->id],
            ['Bunaken', 'bunaken', 'Manado, Sulawesi Utara', 'Sulawesi', 'Taman laut dengan dinding karang dan penyu hijau.', 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1200&q=80', 1.6239, 124.7603, 89, $eco->id],
            ['Wakatobi', 'wakatobi', 'Sulawesi Tenggara', 'Sulawesi', 'Surga diving dengan atol karang dan desa Bajo.', 'https://images.unsplash.com/photo-1559827260-dc66d52bef19?auto=format&fit=crop&w=1200&q=80', -5.5000, 123.7500, 91, $adventure->id],
            ['Ijen Crater', 'ijen-crater', 'Banyuwangi, Jawa Timur', 'Jawa', 'Kawah belerang dengan api biru langka di dunia.', 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1200&q=80', -8.0580, 114.2420, 86, $adventure->id],
            ['Toraja Highlands', 'toraja-highlands', 'Tana Toraja, Sulawesi Selatan', 'Sulawesi', 'Budaya pemakaman unik dan rumah tongkonan megah.', 'https://images.unsplash.com/photo-1555899434-94d1368aa7af?auto=format&fit=crop&w=1200&q=80', -2.9740, 119.8970, 84, $culture->id],
            ['Belitung Island', 'belitung-island', 'Belitung, Bangka Belitung', 'Sumatera', 'Pantai granit raksasa dengan air jernih Laskar Pelangi.', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80', -2.8500, 107.6667, 83, $nature->id],
            ['Lombok Rinjani', 'lombok-rinjani', 'Lombok Timur, NTB', 'Bali & Nusa Tenggara', 'Gunung berapi tertinggi kedua dengan danau Segara Anak.', 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=1200&q=80', -8.4117, 116.4575, 88, $adventure->id],
            ['Yogyakarta Kraton', 'yogyakarta-kraton', 'Yogyakarta, DIY', 'Jawa', 'Pusat budaya Jawa dengan keraton dan Malioboro.', 'https://images.unsplash.com/photo-1596402184320-417e7178b2cd?auto=format&fit=crop&w=1200&q=80', -7.8053, 110.3644, 82, $culture->id],
            ['Derawan Islands', 'derawan-islands', 'Berau, Kalimantan Timur', 'Kalimantan', 'Kepulauan dengan ubur-ubur tanpa sengat dan manta.', 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1200&q=80', 2.2833, 118.2500, 90, $eco->id],
            ['Banda Neira', 'banda-neira', 'Maluku Tengah', 'Maluku', 'Kepulauan rempah bersejarah dengan Gunung Api.', 'https://images.unsplash.com/photo-1516690561799-46d8f74f9abf?auto=format&fit=crop&w=1200&q=80', -4.5333, 129.9000, 87, $culture->id],
            ['Dieng Plateau', 'dieng-plateau', 'Wonosobo, Jawa Tengah', 'Jawa', 'Dataran tinggi vulkanik dengan telaga warna dan candi.', 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1200&q=80', -7.2000, 109.9000, 81, $nature->id],
            ['Nias Surf Coast', 'nias-surf-coast', 'Nias Selatan, Sumatera Utara', 'Sumatera', 'Ombak kelas dunia dan budaya lompat batu ikonik.', 'https://images.unsplash.com/photo-1502680390469-be75c86b636f?auto=format&fit=crop&w=1200&q=80', 0.8000, 97.6333, 80, $adventure->id],
            ['Ujung Kulon', 'ujung-kulon', 'Pandeglang, Banten', 'Jawa', 'Habitat badak jawa dengan hutan hujan pesisir.', 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=1200&q=80', -6.7500, 105.3333, 92, $eco->id],
            ['Tana Beru', 'tana-beru', 'Bulukumba, Sulawesi Selatan', 'Sulawesi', 'Pusat pembuatan perahu pinisi warisan dunia.', 'https://images.unsplash.com/photo-1559827260-dc66d52bef19?auto=format&fit=crop&w=1200&q=80', -5.5500, 120.4000, 78, $culture->id],
        ];

        $extraModels = [];
        foreach ($extra as [$name, $slug, $location, $region, $desc, $image, $lat, $lng, $score, $catId]) {
            $extraModels[] = Destination::create([
                'category_id' => $catId,
                'name' => $name,
                'slug' => $slug,
                'location' => $location,
                'region' => $region,
                'description' => $desc,
                'image' => $image,
                'latitude' => $lat,
                'longitude' => $lng,
                'sustainability_score' => $score,
            ]);
        }

        // Sustainability Indicators
        SustainabilityIndicator::create([
            'destination_id' => $wayKambas->id,
            'eco_transport' => 82,
            'waste_management' => 84,
            'local_community' => 90,
            'conservation' => 94,
        ]);

        SustainabilityIndicator::create([
            'destination_id' => $pahawang->id,
            'eco_transport' => 78,
            'waste_management' => 80,
            'local_community' => 88,
            'conservation' => 82,
        ]);

        SustainabilityIndicator::create([
            'destination_id' => $kiluan->id,
            'eco_transport' => 74,
            'waste_management' => 76,
            'local_community' => 84,
            'conservation' => 82,
        ]);

        SustainabilityIndicator::create([
            'destination_id' => $siger->id,
            'eco_transport' => 76,
            'waste_management' => 70,
            'local_community' => 82,
            'conservation' => 68,
        ]);

        // Activities
        Activity::create([
            'destination_id' => $wayKambas->id,
            'name' => 'Nature Exploration',
            'description' => 'Menjelajahi kawasan alam dengan tetap memperhatikan aturan konservasi.',
            'duration' => '2–3 hours',
        ]);

        Activity::create([
            'destination_id' => $wayKambas->id,
            'name' => 'Wildlife Experience',
            'description' => 'Pengalaman mengenal satwa dan lingkungan konservasi.',
            'duration' => '2 hours',
        ]);

        Activity::create([
            'destination_id' => $pahawang->id,
            'name' => 'Snorkeling',
            'description' => 'Menikmati pengalaman bawah laut dengan menjaga ekosistem pesisir.',
            'duration' => '2 hours',
        ]);

        Activity::create([
            'destination_id' => $pahawang->id,
            'name' => 'Island Exploration',
            'description' => 'Menjelajahi kawasan pulau dan lingkungan pesisir.',
            'duration' => '3 hours',
        ]);

        Activity::create([
            'destination_id' => $kiluan->id,
            'name' => 'Coastal Exploration',
            'description' => 'Menikmati panorama pesisir dan lingkungan sekitar.',
            'duration' => '2 hours',
        ]);

        Activity::create([
            'destination_id' => $siger->id,
            'name' => 'Cultural Experience',
            'description' => 'Mengenal salah satu ikon budaya dan pariwisata Lampung.',
            'duration' => '1–2 hours',
        ]);

        foreach ($extraModels as $model) {
            $base = $model->sustainability_score;
            SustainabilityIndicator::create([
                'destination_id' => $model->id,
                'eco_transport' => max(60, $base - 8),
                'waste_management' => max(60, $base - 6),
                'local_community' => min(98, $base + 2),
                'conservation' => min(98, $base + 4),
            ]);

            Activity::create([
                'destination_id' => $model->id,
                'name' => 'Guided Exploration',
                'description' => 'Jelajahi ' . $model->name . ' bersama pemandu lokal dengan prinsip wisata bertanggung jawab.',
                'duration' => '2–3 hours',
            ]);

            Activity::create([
                'destination_id' => $model->id,
                'name' => 'Local Community Visit',
                'description' => 'Bertemu komunitas lokal dan dukung ekonomi setempat di ' . $model->location . '.',
                'duration' => '2 hours',
            ]);
        }

        $this->call(DestinationSeeder::class);
    }
}