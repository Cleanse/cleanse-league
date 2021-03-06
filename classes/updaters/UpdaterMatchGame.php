<?php namespace Cleanse\League\Classes\Updaters;

use Auth;
use DB;
use Cleanse\League\Classes\ManagerLog\LeagueHandler;
use Cleanse\League\Models\Season;
use Cleanse\League\Models\Match;
use Cleanse\League\Models\MatchGame;
use Cleanse\League\Models\MatchGamePlayer;
use Cleanse\League\Classes\Updaters\UpdaterMatchStats;

class UpdaterMatchGame
{
    public $post;
    public $matchId;
    public $gameId;

    public function update($post)
    {
        $this->post = $post;

        //Return this object so the File class can attach the screenshot.
        $game = $this->createMatchGame();

        $this->createMatchGamePlayers();

        $this->updateMatchScore();

        return $game;
    }

    public function finalize($post)
    {
        $match = Match::with(['games.players'])->find($post['match_id']);

        if ($match->team_one_score > $match->team_two_score) {
            $match->winner_id = $match->team_one;
            $match->save();
        }

        if ($match->team_two_score > $match->team_one_score) {
            $match->winner_id = $match->team_two;
            $match->save();
        }

        $log = new LeagueHandler();

        $log->handle(Auth::getUser(), 'match.game.finalize', $match);

        $this->updateMatchStats($match);

        return $match->winner->team->name;
    }

    public function unlock($post)
    {
        $match = Match::find($post['match_id']);

        $match->winner_id = null;
        $match->save();

        $log = new LeagueHandler();

        $log->handle(Auth::getUser(), 'match.game.unlock', $match);
    }

    protected function createMatchGame()
    {
        $newGame = new MatchGame;
        $newGame->match_id = $this->post['match_id'];
        $newGame->team_one = $this->post['team_one'];
        $newGame->team_two = $this->post['team_two'];
        $newGame->winner_id = $this->post['winner_id'];
        $newGame->duration = $this->convertDuration($this->post['duration']);

        if ($this->post['vod'] !== '') {
            $newGame->vod = $this->post['vod'];
        }

        $newGame->save();

        $this->gameId = $newGame->id;

        return $newGame;
    }

    protected function createMatchGamePlayers()
    {
        $logData = [];

        foreach ($this->post['team_one_roster'] as $oplayer) {

            if ($oplayer['player'] == "0") {
                continue;
            }

            $eventPlayerOne = $this->firstOrCreateEventPlayer($oplayer['player'], $this->post['team_one']);

            $newMatchGamePlayer = new MatchGamePlayer;

            $newMatchGamePlayer->game_id = $this->gameId;
            $newMatchGamePlayer->team_id = $this->post['team_one'];
            $newMatchGamePlayer->game_winner_id = $this->post['winner_id'];
            $newMatchGamePlayer->duration = $this->convertDuration($this->post['duration']);
            $newMatchGamePlayer->player_id = $eventPlayerOne->id;
            $newMatchGamePlayer->player_job = $oplayer['job'];
            $newMatchGamePlayer->medals = $this->checkPostInt($oplayer['medals']);
            $newMatchGamePlayer->kills = $this->checkPostInt($oplayer['kills']);
            $newMatchGamePlayer->deaths = $this->checkPostInt($oplayer['deaths']);
            $newMatchGamePlayer->assists = $this->checkPostInt($oplayer['assists']);
            $newMatchGamePlayer->damage = $this->checkPostInt($oplayer['damage']);
            $newMatchGamePlayer->healing = $this->checkPostInt($oplayer['healing']);

            $newMatchGamePlayer->save();

            $logData[] = $newMatchGamePlayer;
        }

        foreach ($this->post['team_two_roster'] as $tplayer) {

            if ($tplayer['player'] == "0") {
                continue;
            }

            $eventPlayerTwo = $this->firstOrCreateEventPlayer($tplayer['player'], $this->post['team_two']);

            $newMatchGamePlayer = new MatchGamePlayer;

            $newMatchGamePlayer->game_id = $this->gameId;
            $newMatchGamePlayer->team_id = $this->post['team_two'];
            $newMatchGamePlayer->game_winner_id = $this->post['winner_id'];
            $newMatchGamePlayer->duration = $this->convertDuration($this->post['duration']);
            $newMatchGamePlayer->player_id = $eventPlayerTwo->id;
            $newMatchGamePlayer->player_job = $tplayer['job'];
            $newMatchGamePlayer->medals = $this->checkPostInt($tplayer['medals']);
            $newMatchGamePlayer->kills = $this->checkPostInt($tplayer['kills']);
            $newMatchGamePlayer->deaths = $this->checkPostInt($tplayer['deaths']);
            $newMatchGamePlayer->assists = $this->checkPostInt($tplayer['assists']);
            $newMatchGamePlayer->damage = $this->checkPostInt($tplayer['damage']);
            $newMatchGamePlayer->healing = $this->checkPostInt($tplayer['healing']);

            $newMatchGamePlayer->save();

            $logData[] = $newMatchGamePlayer;
        }

        $log = new LeagueHandler();
        $log->handle(Auth::getUser(), 'match.game.create', $logData);
    }

    protected function convertDuration($time)
    {
        $findMe   = ':';
        $needsConvert = strpos($time, $findMe);

        if ($needsConvert) {
            $str_time = $time;

            sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);

            return isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
        }

        return 0;
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
            ->where('value', '=', 1)
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

        $log = new LeagueHandler();

        $log->handle(Auth::getUser(), 'match.game.score', $match);
    }

    protected function updateMatchStats($match)
    {
        $stats = new UpdaterMatchStats();
        $stats->update($match);
    }

    protected function checkPostInt($data)
    {
        if (!empty($data)) {
            return $data;
        }

        return 0;
    }
}
