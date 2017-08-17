<?php namespace Cleanse\League\Classes\Event;

use Cleanse\League\Classes\Scheduler;

class EventScheduler extends Scheduler
{
    public function addMatch($teamA, $teamB, $attachable)
    {
        $season->matches()->firstOrCreate([
            'team_one' => $teamA,
            'team_two' => $teamB
        ]);
    }
}