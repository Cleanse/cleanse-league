<?php namespace Cleanse\League\Updates;

use October\Rain\Database\Updates\Seeder;
use Cleanse\League\Models\Event;
use Cleanse\League\Models\League;
use Cleanse\League\Models\Championship;
use Cleanse\League\Models\Season;
use Cleanse\League\Models\Tournament;
use Cleanse\League\Models\Match;
use Cleanse\League\Models\Team;
use Cleanse\League\Models\EventTeam;
use Cleanse\League\Classes\Season\SeasonScheduler as Scheduler;

class SeedCoreTables extends Seeder
{
    public function run()
    {
        $tryhard = Event::firstOrCreate([
            'name' => 'Team Tryhard Monthly'
        ]);

        $aether = League::firstOrCreate([
            'name' => 'Aether League',
            'about' => 'We want this to be as successful as possible so SE can take note and maybe in the distant 
                        future give PVP more support.'
        ]);

        $championship = $aether->championships()->firstOrCreate([
            'name' => '2017 Aether League Championship Series',
            'championship_rules' => '**Hey** *bae*!'
        ]);

        $season = $championship->seasons()->firstOrCreate([
            'name' => 'Inaugural Season'
        ]);

        /**
         * Create test tournaments.
         */
        $tryhard->tourneys()->firstOrCreate([
            'name' => 'Team Tryhard Monthly Tourney'
        ]);

        $season->tourneys()->firstOrCreate([
            'name' => 'Inaugural Season Tourney'
        ]);

        $tourney = $championship->tourneys()->firstOrCreate([
            'name' => 'Champ Tourney'
        ]);

        /**
         * Add some teams.
         */
        $teams = ['Cats', 'Insomnia', 'RareX', 'Ninjas', 'Dread', 'Lupins', 'Lucky Charms'];

        foreach ($teams as $team) {
            $t_{$team} = Team::firstOrCreate([
                'name' => $team
            ]);

            $season->teams()->firstOrCreate([
                'team_id' => $t_{$team}->id
            ]);
        }

        /**
         * Create Seasonal Schedule
         */
        $schedReqs = [
            'season' => $season->id,
            'weeks' => 12,
            'start' => '2018-1-31'
        ];
        $schedule = new Scheduler;
        $schedule->createSchedule($schedReqs);
    }
}
