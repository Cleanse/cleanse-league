<?php namespace Cleanse\League\Updates;

use October\Rain\Database\Updates\Seeder;
use Cleanse\User\Models\UserGroup;

class SeedLeagueUserGroups extends Seeder
{
    public function run()
    {
        UserGroup::firstOrCreate([
            'name' => 'League Admin',
            'code' => 'league-admin',
            'description' => 'Administrator for the League.'
        ]);

        UserGroup::firstOrCreate([
            'name' => 'League Staff',
            'code' => 'league-staff',
            'description' => 'Staff for the League.'
        ]);
    }
}
