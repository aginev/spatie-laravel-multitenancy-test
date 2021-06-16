<?php

namespace Database\Seeders;

use App\Models\TenantUser;
use Illuminate\Database\Seeder;

class TenantUsersTableSeeder extends Seeder
{
    public function run()
    {
        TenantUser::factory(10)->create();
    }
}
