<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = [
            [
                'name' => 'M.Firdaus Madjid',
                'employee_no' => 'P-004',
                'role' => 'Asisten Tata Usaha',
                'phone' => '+628123456782',
                'email' => 'mfirdaus@example.com',
                'website' => 'https://wa.me/628123456782',
                'avatar_url' => null,
            ],
            [
                'name' => 'Ermawati',
                'employee_no' => 'P-003',
                'role' => 'Krani 1 Tata Usaha',
                'phone' => '+628123456781',
                'email' => 'ermawati@example.com',
                'website' => 'https://wa.me/628123456781',
                'avatar_url' => null,
            ],
            [
                'name' => 'Jumartadi',
                'employee_no' => 'P-002',
                'role' => 'Krani Gudang',
                'phone' => '+628123456780',
                'email' => 'jumartadi@example.com',
                'website' => 'https://wa.me/628123456780',
                'avatar_url' => null,
            ],
            [
                'name' => 'Mujiono',
                'employee_no' => 'P-001',
                'role' => 'Petugas Gudang',
                'phone' => '+628123456789',
                'email' => 'mujiono@example.com',
                'website' => 'https://wa.me/628123456789',
                'avatar_url' => null,
            ],
        ];

        foreach ($members as $m) {
            Member::updateOrCreate(['email' => $m['email']], $m);
        }
    }
}
