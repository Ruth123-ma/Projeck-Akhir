<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()
            ->whereRaw('LOWER(name) = ?', ['admin'])
            ->first();

        if ($user) {
            if ($user->name !== 'Admin') {
                $user->update(['name' => 'Admin']);
            }

            return;
        }

        User::create([
            'name' => 'Admin',
            'email' => 'admin@stockgudang.test',
            'password' => Hash::make('123'),
        ]);
    }
}
