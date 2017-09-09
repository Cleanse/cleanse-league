<?php namespace Cleanse\League\Classes\Updaters;

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

        $this->updateMatchGameTeams();
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
    }

    protected function deleteMatchGame($id)
    {
        $matchGamePlayer = MatchGamePlayer::where('game_id', '=', $id)->delete();

        $matchGame = MatchGame::find($id);

        $matchGame->delete();
    }

    protected function updateMatchGameTeams()
    {
        foreach ($this->post['team_one_roster'] as $player_a) {

            if ($player_a['player'] == "0" && isset($player_a['game_player_id'])) {
                $this->deleteMatchGamePlayer($player_a['game_player_id']);
                continue;
            }

            if ($player_a['player'] == "0") {
                continue;
            }

            if (isset($player_a['game_player_id'])) {
                $this->updateMatchGamePlayer($player_a, $this->post['team_one']);
                continue;
            }

            $this->createMatchGamePlayer($player_a, $this->post['team_one']);
        }

        foreach ($this->post['team_two_roster'] as $player_b) {

            if ($player_b['player'] == "0" && isset($player_b['game_player_id'])) {
                $this->deleteMatchGamePlayer($player_b['game_player_id']);
                continue;
            }

            if ($player_b['player'] == "0") {
                continue;
            }

            if (isset($player_b['game_player_id'])) {
                $this->updateMatchGamePlayer($player_b, $this->post['team_two']);
                continue;
            }

            $this->createMatchGamePlayer($player_b, $this->post['team_two']);
        }
    }

    protected function createMatchGamePlayer($player, $teamId)
    {
        $eventPlayer = $this->firstOrCreateEventPlayer($player['player'], $teamId);

        $newMatchGamePlayer = new MatchGamePlayer;

        $newMatchGamePlayer->game_id = $this->gameId;
        $newMatchGamePlayer->team_id = $teamId;
        $newMatchGamePlayer->player_id = $eventPlayer->id;
        $newMatchGamePlayer->player_job = $player['job'];
        $newMatchGamePlayer->medals = $player['medals'];
        $newMatchGamePlayer->kills = $player['kills'];
        $newMatchGamePlayer->deaths = $player['deaths'];
        $newMatchGamePlayer->assists = $player['assists'];
        $newMatchGamePlayer->damage = $player['damage'];
        $newMatchGamePlayer->healing = $player['healing'];

        $newMatchGamePlayer->save();
    }

    protected function updateMatchGamePlayer($player, $teamId)
    {
        $eventPlayer = $this->firstOrCreateEventPlayer($player['player'], $teamId);

        $editMatchGamePlayer = MatchGamePlayer::find($player['game_player_id']);

        $editMatchGamePlayer->game_id = $this->gameId;
        $editMatchGamePlayer->team_id = $teamId;
        $editMatchGamePlayer->player_id = $eventPlayer->id;
        $editMatchGamePlayer->player_job = $player['job'];
        $editMatchGamePlayer->medals = $player['medals'];
        $editMatchGamePlayer->kills = $player['kills'];
        $editMatchGamePlayer->deaths = $player['deaths'];
        $editMatchGamePlayer->assists = $player['assists'];
        $editMatchGamePlayer->damage = $player['damage'];
        $editMatchGamePlayer->healing = $player['healing'];

        $editMatchGamePlayer->save();
    }

    protected function deleteMatchGamePlayer($id)
    {
        $matchGamePlayer = MatchGamePlayer::find($id);

        $matchGamePlayer->delete();
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
