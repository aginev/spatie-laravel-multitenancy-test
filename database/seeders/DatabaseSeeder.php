<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Database\Factories\LandlordUserFactory;
use Database\Factories\TenantUserFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        Tenant::checkCurrent()
            ? $this->runTenantSpecificSeeders()
            : $this->runLandlordSpecificSeeders();
    }

    public function runTenantSpecificSeeders()
    {
        $this->call(TenantUserFactory::class);
    }

    public function runLandlordSpecificSeeders()
    {
        $this->call(LandlordUserFactory::class);
    }
}
