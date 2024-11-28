<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Marvin Campos',
            'odoo_uid' => 2, // CADA USUARIO TIENE UN UID ÚNICO DEBES IDENTIFICARLO EN EL SISTEMA DE ODOO
            'email' => 'marvinhectorcamposdeza@gmail.com',
            'token' => '7942d56bb14e4407fbad55e3f30c0354bcd99a51',
            'password' => Hash::make('987654321'),
        ]);

        User::create([
            'name' => 'Jhamil Crispin',
            'odoo_uid' => 6, // CADA USUARIO TIENE UN UID ÚNICO DEBES IDENTIFICARLO EN EL SISTEMA DE ODOO
            'email' => 'j99crispin@gmail.com',
            'token' => 'b7946a934c26a7c939127b526096864e29b1dafa',
            'password' => Hash::make('987654321'),
        ]);
    }
}
