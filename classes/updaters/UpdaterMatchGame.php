<?php namespace Cleanse\League\Classes\Updaters;

use DB;
use Cleanse\League\Models\Season;
use Cleanse\League\Models\Match;
use Cleanse\League\Models\MatchGame;
use Cleanse\League\Models\MatchGamePlayer;

class UpdaterMatchGame
{
    public $post;
    public $matchId;
    public $gameId;

    public function update($post)
    {
        $this->post = $post;

        $game = $this->createMatchGame();

        $this->createMatchGamePlayers();

        $this->updateMatchScore();

        return $game;
    }

    public function finalize($post)
    {
        $match = Match::find($post['match_id']);

        if ($match->team_one_score > $match->team_two_score) {
            $match->winner_id = $match->team_one;
            $match->save();

            return $match->winner->team->name;
        }

        if ($match->team_two_score > $match->team_one_score) {
            $match->winner_id = $match->team_two;
            $match->save();

            return $match->winner->team->name;
        }

        //add an error if there's a tie.
    }

    public function unlock($post)
    {
        $match = Match::find($post['match_id']);

        $match->winner_id = null;
        $match->save();
    }

    //Return this object so the File class can attach the screenshot.
    protected function createMatchGame()
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

    protected function createMatchGamePlayers()
    {
        foreach ($this->post['team_one_roster'] as $oplayer) {

            if ($oplayer['player'] == "0") {
                continue;
            }

            $eventPlayerOne = $this->firstOrCreateEventPlayer($oplayer['player'], $this->post['team_one']);

            $newMatchGamePlayer = new MatchGamePlayer;

            $newMatchGamePlayer->game_id = $this->gameId;
            $newMatchGamePlayer->team_id = $this->post['team_one'];
            $newMatchGamePlayer->player_id = $eventPlayerOne->id;
            $newMatchGamePlayer->player_job = $oplayer['job'];
            $newMatchGamePlayer->medals = $oplayer['medals'];
            $newMatchGamePlayer->kills = $oplayer['kills'];
            $newMatchGamePlayer->deaths = $oplayer['deaths'];
            $newMatchGamePlayer->assists = $oplayer['assists'];
            $newMatchGamePlayer->damage = $oplayer['damage'];
            $newMatchGamePlayer->healing = $oplayer['healing'];

            $newMatchGamePlayer->save();
        }

        foreach ($this->post['team_two_roster'] as $tplayer) {

            if ($tplayer['player'] == "0") {
                continue;
            }

            $eventPlayerTwo = $this->firstOrCreateEventPlayer($tplayer['player'], $this->post['team_two']);

            $newMatchGamePlayer = new MatchGamePlayer;

            $newMatchGamePlayer->game_id = $this->gameId;
            $newMatchGamePlayer->team_id = $this->post['team_two'];
            $newMatchGamePlayer->player_id = $eventPlayerTwo->id;
            $newMatchGamePlayer->player_job = $tplayer['job'];
            $newMatchGamePlayer->medals = $tplayer['medals'];
            $newMatchGamePlayer->kills = $tplayer['kills'];
            $newMatchGamePlayer->deaths = $tplayer['deaths'];
            $newMatchGamePlayer->assists = $tplayer['assists'];
            $newMatchGamePlayer->damage = $tplayer['damage'];
            $newMatchGamePlayer->healing = $tplayer['healing'];

            $newMatchGamePlayer->save();
        }
    }

    protected function firstOrCreateEventPlayer($player_id, $team_id)
    {
        $season = Season::find($this->post['season_id']);
        $player = $season->players()->firstOrCreate([
            'player_id' => $player_id,
            'event_team_id' => $team_id
        ]);

        return $player;
    }

    protected function updateMatchScore()
    {
        $match = Match::find($this->post['match_id']);

        $matchGames = MatchGame::where('match_id', '=', $match->id)
            ->select('winner_id', DB::raw('count(*) as total'))
            ->groupBy('winner_id')
            ->get();

        foreach ($matchGames as $matchGame) {
            if ($matchGame->winner_id == $match->team_one) {
                $match->team_one_score = $matchGame->total;
            }

            if ($matchGame->winner_id == $match->team_two) {
                $match->team_two_score = $matchGame->total;
            }
        }

        $match->save();
    }

    protected function updateTeamStats()
    {
        //
    }

    protected function updatePlayerStats()
    {
        //
    }
}
