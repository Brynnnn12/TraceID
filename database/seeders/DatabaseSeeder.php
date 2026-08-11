<?php

namespace Database\Seeders;

use App\Models\BankTransfer;
use App\Models\SocialMedia;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        BankTransfer::create();
        SocialMedia::create();

        User::factory()->create([
            'name' => env('ADMIN_NAME', 'Admin TraceID'),
            'email' => env('ADMIN_EMAIL', 'admin@traceid.test'),
            'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
        ]);
    }
}
