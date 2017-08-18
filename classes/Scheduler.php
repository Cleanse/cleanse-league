<?php namespace Cleanse\League\Classes;

class Scheduler
{
    /**
     * @param $teams
     * @param int $rounds
     * @param bool $randomize
     * @return array|string
     */
    public function buildSchedule($teams, $rounds = 12, $randomize = false)
    {
        if ($randomize) {
            shuffle($teams);
        }

        return $this->createMatches($teams, $rounds);
    }

    /**
     * Thanks https://en.wikipedia.org/wiki/Round-robin_tournament#Scheduling_algorithm
     *
     * @param int $rounds
     * @param array $teams
     * @return array
     */
    public function createMatches($teams, $rounds = 12)
    {
        //Check if team count is odd
        if (count($teams) % 2 !== 0) {
            $teams[] = 'BYE';
        }

        $halfTeams = count($teams) / 2;
        $mTeams = $teams;

        array_shift($mTeams);

        $count = count($mTeams);

        $schedule = [];
        for ($i = 0; $i < $rounds; $i++) {

            $teamIndex = $i % $count;

            $schedule[$i][] = [$teams[0], $mTeams[$teamIndex]];

            for ($j = 1; $j < $halfTeams; $j++) {
                $firstTeam = ($i + $j) % $count;
                $secondTeam = ($i + $count - $j) % $count;

                $schedule[$i][] = [$mTeams[$firstTeam], $mTeams[$secondTeam]];
            }
        }

        return array_reverse($schedule);
    }

    /**
     * @param $date
     * @param int $plus
     * @return false|string
     */
    public function yearWeek($date, $plus = 0)
    {
        $gameDate = strtotime($date);
        $gameDate = strtotime('+' . $plus . ' week', $gameDate);

        return date('Y-m-d H:i:s', $gameDate);
    }
}