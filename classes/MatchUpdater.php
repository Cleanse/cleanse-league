<?php namespace Cleanse\League\Classes;

use Cleanse\League\Models\MatchGame;
use Cleanse\League\Models\MatchGamePlayer;

class MatchUpdater
{
    public $post;
    public $gameId;

    public function updateMatch($post, $final = false)
    {
        $this->post = $post;

        $game = $this->addMatchGame();

        $this->createMatchGamePlayer();

        if ($final) {
            $updateMatch = $post[''];
        }

        return $game;
    }

    protected function addMatchGame()
    {
        $newGame = new MatchGame;
        $newGame->match_id = $this->post['match_id'];
        $newGame->team_one = $this->post['team_one'];
        $newGame->team_two = $this->post['team_two'];
        $newGame->winner_id = $this->post['winner_id'];

        if ($this->post['vod'] !== '') {
            $newGame->vod = $this->post['vod'];
        }

        $newGame->save();

        $this->gameId = $newGame->id;

        return $newGame;
    }

    protected function updateMatchStats()
    {
        //
    }

    protected function updateTeamStats()
    {
        //
    }

    protected function createMatchGamePlayer()
    {
        foreach ($this->post['team_one_roster'] as $player) {

            if ($player['player'] == "0") {
                continue;
            }

            $newMatchGamePlayer = new MatchGamePlayer;

            $newMatchGamePlayer->game_id = $this->gameId;
            $newMatchGamePlayer->player_id = $player['player'];
            $newMatchGamePlayer->player_job = $player['job'];
            $newMatchGamePlayer->medals = $player['medals'];
            $newMatchGamePlayer->kills = $player['kills'];
            $newMatchGamePlayer->deaths = $player['deaths'];
            $newMatchGamePlayer->assists = $player['assists'];
            $newMatchGamePlayer->damage = $player['damage'];
            $newMatchGamePlayer->healing = $player['healing'];

            $newMatchGamePlayer->save();
        }
    }

    protected function updatePlayerStats()
    {
        //
    }
}