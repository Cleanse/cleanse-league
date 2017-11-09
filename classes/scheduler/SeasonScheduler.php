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

        $matches = $this->buildSchedule($season->teams->toArray(), $postData['length'] ?? null);

        $startDate = $postData['start'];

        $count = 1;
        $week = 1;
        $seasonalSchedule = [];
        foreach ($matches as $round) {
            foreach ($round as $match) {
                //Check for BYE weeks.
                if (isset($match[0]['id']) && isset($match[1]['id'])) {
                    $seasonalSchedule[] = $this->addMatch($season, $match[0]['id'], $match[1]['id'], $week);
                }
            }

            if ($count % $postData['per'] == 0) {
                $week++;
            }

            $count++;
        }

        return collect($seasonalSchedule);
    }

    private function addMatch(Season $season, $teamA, $teamB, $week)
    {
        $match = $season->matches()->firstOrCreate([
            'team_one' => $teamA,
            'team_two' => $teamB,
            'takes_place_during' => $week
        ]);

        return $match;
    }
}
