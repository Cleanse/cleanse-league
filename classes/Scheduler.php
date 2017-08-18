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

        //If teams > rounds, create divisions.
        if ($teams > $weeks) {
            //split teams in half
            $divisions = $this->createDivisions($teams);

            //Make a perfect schedule for split teams
            $dSeasons = [];
            foreach ($divisions as $division) {
                $dSeasons[] = $this->createDivisionMatches($division);
            }

            //match teams vs opposing divisions for remaining weeks
        } else {
            return '';
        }

        //Shuffle overall weeks
        /**
         *
         */
        //How many weeks
        //how many teams
        //difference if any between weeks and teams
        //if teams and weeks are equal, then generate schedule so that every team plays each other once
        //if there are less teams than weeks then make every team play each other once, then an extra round of games
        //to fill out the extra weeks
        //if there are more teams than weeks, then generate as many matches as possible.

        $input = [
            [0 => 'Ab'],
            [1 => 'Cd'],
            [2 => 'Ef'],
            [3 => 'Gh'],
            [4 => 'Ij'],
            [5 => 'Kl'],
            [6 => 'Mn'],
            [7 => 'Op']
        ];

        for ($i = 0; $i < $weeks; $i++) {
            $a = $input[$i];
            for ($j = $i + 1; $j < sizeof($input); $j++) {
                $b = $input[$j];

                echo $a . ' vs ' . $b;
            }
        }
    }

    public function createDivisions($teams)
    {
        $size = count($teams) / 2;
        $newTeams = array_chunk($teams, $size, true);

        foreach ($newTeams as $division) {
            $divSize = count($division);

            for ($i = 0; $i < $divSize; $i++) {
                $a = $division[$i];
                for ($j = $i + 1; $j < $divSize; $j++) {
                    $b = $division[$j];

                    $divisions[] = [$a, $b];
                }
            }
        }

        return $divisions;
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
     * WIP, Needs to replace byes with an opposing division match.
     * @param $teams
     * @return array
     */
    public function createDivisionMatches($teams)
    {
        //Check if team count is odd
        if (count($teams) % 2 !== 0) {
            $teams[] = 'BYE';
        }

        $rounds = count($teams);

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

    public function weeksTeamsDiff($weeks, $teams)
    {
        $diff = $weeks - $teams;
        if ($diff < 0) $diff *= -1;
        return $diff;
    }

    public function yearWeek($date, $plus = 0)
    {
        $gameDate = strtotime($date);
        $gameDate = strtotime('+'.$plus.' week', $gameDate);

        return date('YW', $gameDate);
    }
}