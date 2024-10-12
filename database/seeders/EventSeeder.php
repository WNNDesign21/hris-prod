<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = [
            [
                'jenis_event' => 'EP',
                'organisasi_id' => 3,
                'keterangan' => 'TCF Tujuh Belas Agustusan',
                'durasi' => 1,
                'tanggal_mulai' =>  '2024-08-17',
                'tanggal_selesai' => '2024-08-17'
            ],
            [
                'jenis_event' => 'EP',
                'organisasi_id' => 3,
                'keterangan' => 'TCF Family Gathering',
                'durasi' => 2,
                'tanggal_mulai' =>  '2024-12-24',
                'tanggal_selesai' => '2024-12-25'
            ],
            [
                'jenis_event' => 'EP',
                'organisasi_id' => null,
                'keterangan' => 'TCF Job Fair',
                'durasi' => 2,
                'tanggal_mulai' =>  '2024-11-24',
                'tanggal_selesai' => '2024-11-24'
            ]
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}
