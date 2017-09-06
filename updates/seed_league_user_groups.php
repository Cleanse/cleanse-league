<?php namespace Cleanse\League\Updates;

use October\Rain\Database\Updates\Seeder;
use Cleanse\User\Models\UserGroup;

class SeedLeagueUserGroups extends Seeder
{
    public function run()
    {
        if (!UserGroup::whereCode('league-admin')->count() > 0) {
            UserGroup::firstOrCreate([
                'name' => 'League Admin',
                'code' => 'league-admin',
                'description' => 'Administrator for the League.'
            ]);
        }

        if (!UserGroup::whereCode('league-staff')->count() > 0) {
            UserGroup::firstOrCreate([
                'name' => 'League Staff',
                'code' => 'league-staff',
                'description' => 'Staff for the League.'
            ]);
        }
    }
}
