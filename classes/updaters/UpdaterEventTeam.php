<?php namespace Cleanse\League\Classes\Updaters;
/**
 * Created by PhpStorm.
 * User: paullovato
 * Date: 9/8/17
 * Time: 7:58 PM
 */
class UpdaterEventTeam
{
    public $id;
    public $team;

    public function buildStats($id)
    {
        $this->id = $id;

        $this->team = Match::where('team_one', '=', $this->id)
            ->orWhere('team_two', '=', $this->id)
            ->get();

        return [
            'matches' => $this->totalMatches(),
            'wins' => $this->matchWins(),
            'losses' => $this->matchLosses(),
            'games' => $this->totalGames(),
            'game_wins' => $this->gameWins(),
            'game_losses' => $this->gameLosses()
        ];
    }

    protected function totalMatches()
    {
        return $this->team->whereNotNull('winner_id')->count();
    }

    protected function matchWins()
    {
        return $this->team->where('winner_id', '=', $this->id)->count();
    }

    protected function matchLosses()
    {
        return $this->totalMatches() - $this->matchWins();
    }

    protected function totalGames()
    {
        $ta_wins = $this->team->sum('team_one_score');
        $tb_wins = $this->team->sum('team_two_score');

        return $ta_wins + $tb_wins;
    }

    protected function gameWins()
    {
        $ta_wins = 0;
        foreach ($this->team as $ta) {
            if ($ta->team_one == $this->id) {
                $ta_wins = $ta_wins + $ta->team_one_score;
            }
        }

        $tb_wins = 0;
        foreach ($this->team as $tb) {
            if ($tb->team_two == $this->id) {
                $tb_wins = $tb_wins + $tb->team_two_score;
            }
        }

        $wins = $ta_wins + $tb_wins;

        return $wins;
    }

    protected function gameLosses()
    {
        //
    }

    protected function gameTies()
    {
        //scrap ties?
    }
}
