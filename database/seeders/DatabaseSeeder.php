<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            PermissionRoleTableSeeder::class,
            SupervisorTypeSeeder::class,
        ]);

        $this->callOnce([
            CompanySeeder::class,
            ChannelSeeder::class,
            UsersTableSeeder::class,
            RoleUserTableSeeder::class,
            CustomerSeeder::class,

            // product unit detail
            CoveringSeeder::class,
            ColourSeeder::class,

            ProductTagSeeder::class,
            ProductCategorySeeder::class,
            ProductSeeder::class,
            LeadSeeder::class,
            AddressSeeder::class,
            ActivitySeeder::class,
            ActivityCommentSeeder::class,
            QaSeeder::class,
            ProductListSeeder::class,
            DiscountSeeder::class,
        ]);
    }

    /**
     * Seeder that should only ever be called once.
     * We check seeders table for seeded class and record
     * them once seeding is completed.
     *
     * @param $class
     */
    public function callOnce($class)
    {
        $class = collect($class)
            ->filter(function ($class_name) {
                return is_null(\App\Models\Seeder::where('seeders', $class_name)->first());
            })
            ->values()
            ->all();

        $this->call($class);

        collect($class)->each(function ($class_name) {
            return \App\Models\Seeder::create(['seeders' => $class_name]);
        });
    }
}
