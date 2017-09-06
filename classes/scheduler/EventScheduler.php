<?php namespace Cleanse\League\Classes\Scheduler;

use Log;
use Cleanse\League\Classes\Scheduler;
use Cleanse\League\Models\Event;

class EventScheduler extends Scheduler
{
    public function createSchedule($postData)
    {
        if (!isset($postData['event'])) {
            $error = 'Error, no event was selected.';

            Log::error($error);
            return $error;
        }

        $event = Event::whereId($postData['event'])->with('teams', 'matches')->first();

        if ($event->matches()->count() > 0) {
            $error = 'Error, this event has a schedule.';

            Log::error($error);
            return;
        }

        $matches = $this->buildSchedule($event->teams->toArray(), $postData['rounds'] ?? null);

        $startDate = $postData['start'];

        $w = 0;
        foreach ($matches as $round) {
            $date = $this->yearWeek($startDate, $w);

            foreach ($round as $match) {
                $this->addMatch($event, $match[0]['id'], $match[1]['id'], $date);
            }

            $w++;
        }
    }

    private function addMatch(Event $event, $teamA, $teamB, $date)
    {
        $event->matches()->firstOrCreate([
            'team_one' => $teamA,
            'team_two' => $teamB,
            'takes_place_at' => $date
        ]);
    }
}