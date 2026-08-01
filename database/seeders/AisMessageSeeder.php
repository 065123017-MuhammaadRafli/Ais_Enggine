<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AisMessage;
use MongoDB\BSON\UTCDateTime;
use stdClass;

class AisMessageSeeder extends Seeder
{
    public function run()
    {
        // Bersihkan data lama di MongoDB Atlas agar tidak menumpuk
        AisMessage::truncate();

        $data = [];

        for ($i = 0; $i < 100; $i++) {
            // Acak koordinat di sekitar Jakarta berdasarkan latitude referensi Anda (-6.1753942)
            $lat = -6.1753942 + (mt_rand(-10000, 10000) / 100000);
            $lng = 106.8271533 + (mt_rand(-20000, 20000) / 100000);

            // Acak kecepatan dan arah
            $heading = mt_rand(0, 359);
            $sog = mt_rand(0, 250) / 10; // Speed 0.0 - 25.0

            // MMSI acak
            $mmsi = 366053200 + $i;

            // Masukkan struktur JSON persis sesuai permintaan Anda
            $data[] = [
                "sample_key" => "type_1_2_3_sample",
                "actual_message_type_id" => 1,
                "collection_label" => "type_1_2_3",
                "course_over_ground" => $heading - 0.5,
                "decoded_json" => new stdClass(), // Menghasilkan objek kosong {} di MongoDB
                "latitude" => $lat,
                "location" => [
                    "type" => "Point",
                    "coordinates" => [$lng, $lat]
                ],
                "longitude" => $lng,
                "maneuver_indicator" => 0,
                "message_name" => "Position Report Class A",
                "message_type_ids" => [1, 2, 3],
                "navigation_status" => [
                    "navigation_status_id" => 0,
                    "name" => ($sog > 0) ? "Under way using engine" : "Moored"
                ],
                "position_accuracy" => true,
                "radio_status" => 123456,
                "raim" => false,
                "rate_of_turn_raw" => 0,
                "raw_bits" => null,
                "received_at" => new UTCDateTime(now()), // Menghasilkan objek $date di MongoDB
                "repeat_indicator" => 0,
                "second_utc" => mt_rand(0, 59),
                "source_table_pg" => "ais.position_report_class_a",
                "speed_over_ground" => $sog,
                "true_heading" => $heading,
                "mmsi" => $mmsi
            ];
        }

        // Simpan 100 data tersebut ke MongoDB Atlas
        AisMessage::insert($data);

        $this->command->info('100 Data Kapal Lengkap berhasil disimpan ke MongoDB Atlas!');
    }
}
