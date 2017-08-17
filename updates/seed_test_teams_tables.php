<?php namespace Cleanse\League\Updates;

use October\Rain\Database\Updates\Seeder;
use Cleanse\League\Models\Team;
use Cleanse\League\Models\Season;
use Cleanse\League\Models\Tournament;

class SeedTestTeamsTables extends Seeder
{
    public function run()
    {
//        $catsomnia = Team::firstOrCreate([
//            'name' => 'Catsomnia'
//        ]);
//
//        $currentSeason = Season::orderBy('id', 'desc')->first();
//        $championshipBracket = Tournament::where('name', 'Champ Tourney')->first();
//
//        $currentSeason->teams()->attach($catsomnia);
//        $championshipBracket->teams()->attach($catsomnia);
    }
}
