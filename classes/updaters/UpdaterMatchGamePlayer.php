<?php namespace Cleanse\League\Classes\Updaters;

use Auth;
use Cleanse\League\Classes\ManagerLog\LeagueHandler;
use Cleanse\League\Models\Season;
use Cleanse\League\Models\MatchGame;
use Cleanse\League\Models\MatchGamePlayer;

class UpdaterMatchGamePlayer
{
    public $post;
    public $gameId;

    public function update($post)
    {
        $this->post = $post;
        $this->gameId = $this->post['game_id'];

        $this->updateMatchGame();

        $this->updateMatchGameRosters();
    }

    public function delete($post)
    {
        $this->deleteMatchGame($post['game_id']);
    }

    protected function updateMatchGame()
    {
        $editMatchGame = MatchGame::find($this->gameId);
        $editMatchGame->match_id = $this->post['match_id'];
        $editMatchGame->team_one = $this->post['team_one'];
        $editMatchGame->team_two = $this->post['team_two'];
        $editMatchGame->winner_id = $this->post['winner_id'];

        if ($this->post['vod'] !== '') {
            $editMatchGame->vod = $this->post['vod'];
        }

        $editMatchGame->save();

        $log = new LeagueHandler();

        $log->handle(Auth::getUser(), 'match.game.edit', $editMatchGame);
    }

    protected function deleteMatchGame($id)
    {
        MatchGamePlayer::where('game_id', '=', $id)->delete();

        $matchGame = MatchGame::find($id);

        $matchGame->delete();

        $log = new LeagueHandler();

        $log->handle(Auth::getUser(), 'match.game.delete', $matchGame);
    }

    protected function updateMatchGameRosters()
    {
        $logData = [];

        foreach ($this->post['team_one_roster'] as $player_a) {

            if ($player_a['player'] == "0" && isset($player_a['game_player_id'])) {
                $logData[] = $this->deleteMatchGamePlayer($player_a['game_player_id']);
                continue;
            }

            if ($player_a['player'] == "0") {
                continue;
            }

            if (isset($player_a['game_player_id'])) {
                $logData[] = $this->updateMatchGamePlayer($player_a, $this->post['team_one']);
                continue;
            }

            $logData[] = $this->createMatchGamePlayer($player_a, $this->post['team_one']);
        }

        foreach ($this->post['team_two_roster'] as $player_b) {

            if ($player_b['player'] == "0" && isset($player_b['game_player_id'])) {
                $logData[] = $this->deleteMatchGamePlayer($player_b['game_player_id']);
                continue;
            }

            if ($player_b['player'] == "0") {
                continue;
            }

            if (isset($player_b['game_player_id'])) {
                $logData[] = $this->updateMatchGamePlayer($player_b, $this->post['team_two']);
                continue;
            }

            $logData[] = $this->createMatchGamePlayer($player_b, $this->post['team_two']);
        }

        $log = new LeagueHandler();
        $log->handle(Auth::getUser(), 'match.game.roster', $logData);
    }

    protected function createMatchGamePlayer($player, $teamId)
    {
        $eventPlayer = $this->firstOrCreateEventPlayer($player['player'], $teamId);

        $newMatchGamePlayer = new MatchGamePlayer;

        $newMatchGamePlayer->game_id = $this->gameId;
        $newMatchGamePlayer->team_id = $teamId;
        $newMatchGamePlayer->game_winner_id = $this->post['winner_id'];
        $newMatchGamePlayer->player_id = $eventPlayer->id;
        $newMatchGamePlayer->player_job = $player['job'];
        $newMatchGamePlayer->medals = $player['medals'];
        $newMatchGamePlayer->kills = $player['kills'];
        $newMatchGamePlayer->deaths = $player['deaths'];
        if (!empty($player['assists'])) {
            $newMatchGamePlayer->assists = $player['assists'];
        } else {
            $newMatchGamePlayer->assists = 0;
        }
        $newMatchGamePlayer->damage = $player['damage'];
        $newMatchGamePlayer->healing = $player['healing'];

        $newMatchGamePlayer->save();

        return $newMatchGamePlayer;
    }

    protected function updateMatchGamePlayer($player, $teamId)
    {
        $eventPlayer = $this->firstOrCreateEventPlayer($player['player'], $teamId);

        $editMatchGamePlayer = MatchGamePlayer::find($player['game_player_id']);

        $editMatchGamePlayer->game_id = $this->gameId;
        $editMatchGamePlayer->team_id = $teamId;
        $editMatchGamePlayer->game_winner_id = $this->post['winner_id'];
        $editMatchGamePlayer->player_id = $eventPlayer->id;
        $editMatchGamePlayer->player_job = $player['job'];
        $editMatchGamePlayer->medals = $player['medals'];
        $editMatchGamePlayer->kills = $player['kills'];
        $editMatchGamePlayer->deaths = $player['deaths'];
        $editMatchGamePlayer->assists = $player['assists'];
        $editMatchGamePlayer->damage = $player['damage'];
        $editMatchGamePlayer->healing = $player['healing'];

        $editMatchGamePlayer->save();

        return $editMatchGamePlayer;
    }

    protected function deleteMatchGamePlayer($id)
    {
        $matchGamePlayer = MatchGamePlayer::find($id);

        $matchGamePlayer->delete();

        return $matchGamePlayer;
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
}
