<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!State::get()->isEmpty()) {
            return;
        }

        $states = [
            ['iso_code' => 'AL', 'name' => 'Alabama'],
            ['iso_code' => 'AK', 'name' => 'Alaska'],
            ['iso_code' => 'AZ', 'name' => 'Arizona'],
            ['iso_code' => 'AR', 'name' => 'Arkansas'],
            ['iso_code' => 'CA', 'name' => 'California'],
            ['iso_code' => 'CO', 'name' => 'Colorado'],
            ['iso_code' => 'CT', 'name' => 'Connecticut'],
            ['iso_code' => 'DE', 'name' => 'Delaware'],
            ['iso_code' => 'FL', 'name' => 'Florida'],
            ['iso_code' => 'GA', 'name' => 'Georgia'],
            ['iso_code' => 'HI', 'name' => 'Hawaii'],
            ['iso_code' => 'ID', 'name' => 'Idaho'],
            ['iso_code' => 'IL', 'name' => 'Illinois'],
            ['iso_code' => 'IN', 'name' => 'Indiana'],
            ['iso_code' => 'IA', 'name' => 'Iowa'],
            ['iso_code' => 'KS', 'name' => 'Kansas'],
            ['iso_code' => 'KY', 'name' => 'Kentucky'],
            ['iso_code' => 'LA', 'name' => 'Louisiana'],
            ['iso_code' => 'ME', 'name' => 'Maine'],
            ['iso_code' => 'MD', 'name' => 'Maryland'],
            ['iso_code' => 'MA', 'name' => 'Massachusetts'],
            ['iso_code' => 'MI', 'name' => 'Michigan'],
            ['iso_code' => 'MN', 'name' => 'Minnesota'],
            ['iso_code' => 'MS', 'name' => 'Mississippi'],
            ['iso_code' => 'MO', 'name' => 'Missouri'],
            ['iso_code' => 'MT', 'name' => 'Montana'],
            ['iso_code' => 'NE', 'name' => 'Nebraska'],
            ['iso_code' => 'NV', 'name' => 'Nevada'],
            ['iso_code' => 'NH', 'name' => 'New Hampshire'],
            ['iso_code' => 'NJ', 'name' => 'New Jersey'],
            ['iso_code' => 'NM', 'name' => 'New Mexico'],
            ['iso_code' => 'NY', 'name' => 'New York'],
            ['iso_code' => 'NC', 'name' => 'North Carolina'],
            ['iso_code' => 'ND', 'name' => 'North Dakota'],
            ['iso_code' => 'OH', 'name' => 'Ohio'],
            ['iso_code' => 'OK', 'name' => 'Oklahoma'],
            ['iso_code' => 'OR', 'name' => 'Oregon'],
            ['iso_code' => 'PA', 'name' => 'Pennsylvania'],
            ['iso_code' => 'RI', 'name' => 'Rhode Island'],
            ['iso_code' => 'SC', 'name' => 'South Carolina'],
            ['iso_code' => 'SD', 'name' => 'South Dakota'],
            ['iso_code' => 'TN', 'name' => 'Tennessee'],
            ['iso_code' => 'TX', 'name' => 'Texas'],
            ['iso_code' => 'UT', 'name' => 'Utah'],
            ['iso_code' => 'VT', 'name' => 'Vermont'],
            ['iso_code' => 'VA', 'name' => 'Virginia'],
            ['iso_code' => 'WA', 'name' => 'Washington'],
            ['iso_code' => 'WV', 'name' => 'West Virginia'],
            ['iso_code' => 'WI', 'name' => 'Wisconsin'],
            ['iso_code' => 'WY', 'name' => 'Wyoming'],
        ];

        State::insert($states);
    }
}
