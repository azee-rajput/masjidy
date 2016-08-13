<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UsersTableSeeder extends Seeder
{
    public function run()
    {
       \App\User::create([
           'email' => 'test@dummy.com',
           'name'=> 'dummy',
           'password'=>Hash::make('test123')
       ]);
    }
}
