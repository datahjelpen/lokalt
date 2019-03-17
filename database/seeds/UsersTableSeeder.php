<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

use Carbon\Carbon;

use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = new User;
        $user->name = 'Bjørnar';
        $user->email = 'bjornar@datahjelpen.no';
        $user->password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; // password
        $user->email_verified_at = Carbon::now();
        $user->remember_token = Str::random(10);
        $user->save();

        $user->assignRole('superadmin');
        $user->assignRole('admin');
        $user->save();

        if (App::environment('local')) {
            factory(User::class, 50)->create();
        }
    }
}
