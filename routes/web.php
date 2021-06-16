<?php

use App\Actions\MigrateTenantDatabaseAction;
use App\Actions\SeedTenantDatabaseAction;
use App\Jobs\TenantUserJob;
use App\Models\Tenant;
use App\Models\TenantUser;
use Faker\Factory;
use Illuminate\Support\Facades\Route;

$domain = explode('://', config('app.url'))[1];

Route::group([
    'domain' => "admin.{$domain}",
], function () {
    Route::get('/', function () {
        return view('welcome');
    });

    Route::post('/', function (
        MigrateTenantDatabaseAction $migrate,
        SeedTenantDatabaseAction $seed
    ) {
        $faker = Factory::create();
        $index = Tenant::query()->max('id') + 1;

        $tenant = new Tenant();
        $tenant->name = $faker->company;
        $tenant->database = "multitenancy-{$index}";
        $tenant->domain = "t{$index}.multitenancy.test";
        $tenant->save();

        $migrate->execute($tenant);
        $seed->execute($tenant);

        return response()->json($tenant);
    });
});

Route::group([
    'domain' => "{company}.{$domain}",
    'middleware' => 'tenant',
], function () {
    Route::get('/', function () {
        $randomUser = TenantUser::query()->inRandomOrder()->first();

        TenantUserJob::dispatch($randomUser)->onQueue('users');

        return "{$randomUser->name} job dispatched!";
    });
});
