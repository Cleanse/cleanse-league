<?php namespace Cleanse\League\Classes\Scheduler;

use Log;
use Cleanse\League\Classes\Scheduler\Scheduler;
use Cleanse\League\Models\Season;

class SeasonScheduler extends Scheduler
{
    public function createSchedule($postData)
    {
        if (!isset($postData['season'])) {
            $error = 'Error, no season was selected.';

            Log::error($error);
            return $error;
        }

        $season = Season::whereId($postData['season'])->with('teams', 'matches')->first();

        if ($season->matches()->count() > 0) {
            $error = 'Error, this season has a schedule.';

            Log::error($error);
            return;
        }

        $matches = $this->buildSchedule($season->teams->toArray(), $postData['weeks'] ?? null);

        $startDate = $postData['start'];

        $w = 0;
        foreach ($matches as $week) {
            $date = $this->yearWeek($startDate, $w);

            foreach ($week as $match) {
                //Check for BYE weeks.
                if (isset($match[0]['id']) && isset($match[1]['id'])) {
                    $this->addMatch($season, $match[0]['id'], $match[1]['id'], $date);
                }
            }

            $w++;
        }
    }

    private function addMatch(Season $season, $teamA, $teamB, $date)
    {
        $season->matches()->firstOrCreate([
            'team_one' => $teamA,
            'team_two' => $teamB,
            'takes_place_at' => $date
        ]);
    }
}
