<?php namespace Cleanse\League\Classes\Updaters;

use Cleanse\League\Classes\Updaters\UpdaterEventTeam;
use Cleanse\League\Classes\Updaters\UpdaterEventPlayer;

class UpdaterMatchStats
{
    public function update($match)
    {
        //Get and update team stats.
        $teams = [$match->team_one, $match->team_two];

        $this->updateTeams($teams);

        //Get and update player stats.
        $players = [];
        foreach ($match->games as $games) {
            foreach ($games->players as $gamePlayers) {
                $players[] = $gamePlayers->player_id;
            }
        }

        $this->updatePlayers($players);
    }

    protected function updateTeams($teams)
    {
        foreach ($teams as $team) {
            $eTeam = new UpdaterEventTeam;

            $eTeam->calculateStats($team);
        }
    }

    protected function updatePlayers($players)
    {
        foreach ($players as $player) {
            $ePlayer = new UpdaterEventPlayer;

            $ePlayer->calculateStats($player);
        }
    }
}
