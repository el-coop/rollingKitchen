<?php

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        factory(Admin::class)->create()->each(function ($admin){
            $user = factory(User::class)->make([
                'email' => 'admin@kitchen.com',
                'password' => bcrypt(123456)
            ]);
            $admin->user()->save($user);
        });
    }
}
