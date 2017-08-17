<?php namespace Cleanse\League\Updates;

use October\Rain\Database\Updates\Seeder;
use Cleanse\User\Models\UserGroup;

class SeedLeagueUserGroups extends Seeder
{
    public function run()
    {
        UserGroup::firstOrCreate([
            'name' => 'Aether League Admin',
            'code' => 'aether-admin',
            'description' => 'Administrator for the Aether League.'
        ]);

        UserGroup::firstOrCreate([
            'name' => 'Aether League Staff',
            'code' => 'aether-staff',
            'description' => 'Staff for the Aether League.'
        ]);
    }
}
