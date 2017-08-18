<?php namespace cleanse\league\classes\season;

use Cleanse\League\Classes\Scheduler;
use Cleanse\League\Models\Season;

class SeasonScheduler extends Scheduler
{
    public function createSchedule($postData)
    {
        if (!isset($postData['season'])) {
            return 'Error, no season was selected.';
        }

        $season = Season::whereId($postData['season'])->with('teams', 'matches')->first();

        if ($season->matches()->count() > 0) {
            return 'Error, this season has a schedule.';
        }

        $matches = $this->buildSchedule($season->teams()->toArray(), $postData['weeks'] ?? null);

        $startDate = $postData['start'];

        $w = 0;
        foreach ($matches as $match) {
            $date = $this->yearWeek($startDate, $w);

            foreach ($match as $m) {
                $this->addMatch($season, $m[$aId], $m[$bId], $date);
            }

            $w++;
        }
    }

    private function addMatch($season, $teamA, $teamB, $date)
    {
        $season->matches()->firstOrCreate([
            'team_one' => $teamA,
            'team_two' => $teamB,
            'takes_place_at' => $date
        ]);
    }
}