<?php
declare(strict_types=1);

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        for ($i=1; $i <= 10; $i++) { 
            DB::table('users')->insert([
                'username' => 'user.test.' . $i,
                'password' => '123456',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }        
    }
}
