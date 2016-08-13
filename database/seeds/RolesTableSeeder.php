<?php

use Illuminate\Database\Seeder;
use App\Role;


class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $owner = new Role();
        $owner->name = 'owner';
        $owner->display_name = 'Project owner'; // optional
        $owner -> description = 'User is the owner of a given project'; // optional
        $owner->save();
        
         $owner = new Role();
        $owner->name = 'admin';
        $owner->display_name = 'Admin user'; // optional
        $owner -> description = 'User is the owner of a given project'; // optional
        $owner->save();
    }
}
