<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Clinic;
use App\Models\ClinicUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $clinic = Clinic::updateOrCreate(
            ['clinic_name' => 'Main Branch'],
            [
                'address'      => 'Bacolod',
                'phone_number' => '09171234567',
            ]
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'first_name'   => 'admin',
                'last_name'    => 'Admin',
                'password'     => Hash::make('password123'),
                'phone_number' => '09171234567',
                'role'         => 'admin',
                'prc_id'       => null,
            ]
        );

        $doctor = User::updateOrCreate(
            ['email' => 'doctor@gmail.com'],
            [
                'first_name'   => 'doctor',
                'last_name'    => 'Doctor',
                'password'     => Hash::make('password123'),
                'phone_number' => '09171234567',
                'role'         => 'doctor',
                'prc_id'       => null,
            ]
        );

        $staff = User::updateOrCreate(
            ['email' => 'staff@gmail.com'],
            [
                'first_name'   => 'staff',
                'last_name'    => 'Staff',
                'password'     => Hash::make('password123'),
                'phone_number' => '09171234567',
                'role'         => 'assistant',
                'prc_id'       => null,
            ]
        );

        ClinicUser::updateOrCreate([
            'clinic_id' => $clinic->id,
            'user_id'   => $admin->id,
        ]);

        ClinicUser::updateOrCreate([
            'clinic_id' => $clinic->id,
            'user_id'   => $doctor->id,
        ]);

        ClinicUser::updateOrCreate([
            'clinic_id' => $clinic->id,
            'user_id'   => $staff->id,
        ]);
    }
}