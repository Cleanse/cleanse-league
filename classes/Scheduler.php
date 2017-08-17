<?php namespace Cleanse\League\Classes;

class Scheduler
{
    /**
     * @param int $weeks
     * @param int $teams
     */
    public function buildSchedule($weeks = 12, $teams = 8)
    {
        return $this->aah();

        $weeks = 12;
        $teams = [
            [0 => 'Ab'],
            [1 => 'Cd'],
            [2 => 'Ef'],
            [3 => 'Gh'],
            [4 => 'Ij'],
            [5 => 'Kl'],
            [6 => 'Op']
        ];

        //Check if team count is odd
        if ($teams % 2 !== 0) {
            $byeKey = count($teams) + 1;
            $teams[] = [$byeKey => 'BYE'];
        }

        //split teams in half
        if ($teams > $weeks) {
            $size = count($teams) / 2;
            $newTeams = array_chunk($teams, $size, true);

            foreach ($newTeams as $division) {
                $divSize = count($division);

                for ($i = 0; $i < $divSize; $i++) {
                    $a = $division[$i];
                    for ($j = $i + 1; $j < $divSize; $j++) {
                        $b = $division[$j];

                        $perfect[] = [$a, $b];
                    }
                }
            }

            print_r($perfect);

            //split teams in half
            //Make a perfect schedule for split teams

            //match teams vs opposing divisions for remaining weeks
        } else {
            //Make a perfect schedule for teams

            //slice middle chunk of perfect as new var
            //reverse new var
            //append new var to perfect.
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

    public function countTeams($input)
    {
        for ($i = 0; $i < sizeof($input); $i++) {
            $k = $input[$i];
            for ($j = $i + 1; $j < sizeof($input); $j++) {
                $v = $input[$j];

                $season->matches()->firstOrCreate([
                    'team_one' => $k['id'],
                    'team_two' => $v['id']
                ]);
            }
        }
    }

    /**
     * Thanks https://en.wikipedia.org/wiki/Round-robin_tournament#Scheduling_algorithm
     *
     * @param int $weeks
     * @param array $teams
     * @return array
     */
    public function aah($weeks = 12, $teams = [1, 2, 3, 4, 5, 6, 7, 8])
    {
        $halfTeams = count($teams) / 2;

        $mTeams = $teams;
        array_shift($mTeams);
        $count = count($mTeams);

        $schedule = [];
        for ($i = 0; $i < $weeks; $i++) {
            $teamIndex = $i % $count;

            $schedule[$i][] = [$teams[0], $mTeams[$teamIndex]];

            for ($j = 1; $j < $halfTeams; $j++) {
                $teamA = ($i + $j) % $count;
                $teamB = ($i  + $count - $j) % $count;

                $schedule[$i][] = [$mTeams[$teamA], $mTeams[$teamB]];
            }
        }

        return $schedule;
    }

    private function addMatch($teamA, $teamB, $date)
    {
        $season->matches()->firstOrCreate([
            'team_one' => $teamA,
            'team_two' => $teamB,
            'takes_place_at' => $date
        ]);
    }

    public function weeksTeamsDiff($weeks, $teams)
    {
        $diff = $weeks - $teams;
        if ($diff < 0) $diff *= -1;
        return $diff;
    }

    public function yearWeek()
    {
        return date("YW");
    }
}