<?php

namespace robijnsbos_dt_app\database\seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $jsonString = file_get_contents(base_path('database/data/content/users.json'));
        $data = json_decode($jsonString, true); // decode the JSON into an array

        foreach ($data as $entry) {
            if (User::firstWhere('id', $entry['id'])) {
                continue;
            }

            $user = new User($entry);
            $user->password = Hash::make($entry['password']);

            $user->save();
        }
    }
}
