<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AddEventSeeder extends Seeder
{
    public function run()
    {
		$date = array(
			'all' => array(
				'start' => '10:00',
				'end' => '17:00',
			),
		);

        DB::table('events')->insert([
            'user_id' => 2,
            'title' => 'Sample Event',
            'event_code' => Str::uuid(),
            'description' => 'Sample description',
            'confirmation_message' => 'Sample confirmation message',
            'available_datetime' => json_encode($date),
            'start_day_length' => 1,
            'count' => null,
            'is_active' => true,
            'send_email_flg' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}