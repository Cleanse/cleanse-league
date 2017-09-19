<?php namespace Cleanse\League\Classes\Updaters;

use Cleanse\League\Models\Match;
use Cleanse\League\Models\EventTeam;

class UpdaterEventTeam
{
    public $id;
    public $matches;

    public function calculateStats($teamId)
    {
        $team = EventTeam::find($teamId);

        $this->id = $team->id;
        $this->matches = Match::where(function ($query) use ($team) {
            $query->where('team_one', '=', $team->id)
                ->where('matchable_id', '=', $team->teamable_id)
                ->where('matchable_type', '=', $team->teamable_type)
                ->whereNotNull('winner_id');
        })
            ->orWhere(function ($query) use ($team) {
                $query->where('team_two', '=', $team->id)
                    ->where('matchable_id', '=', $team->teamable_id)
                    ->where('matchable_type', '=', $team->teamable_type)
                    ->whereNotNull('winner_id');
            })
            ->get();

        $team->match_total = $this->matchTotal();
        $team->match_wins = $this->matchWins();
        $team->match_losses = $this->matchLosses();
        $team->game_total = $this->gameTotal();
        $team->game_wins = $this->gameWins();
        $team->game_losses = $this->gameLosses();
        $team->game_ties = 0;

        $team->save();

    }

    protected function matchTotal()
    {
        return $this->matches->count();
    }

    protected function matchWins()
    {
        return $this->matches
            ->where('winner_id', $this->id)
            ->count();
    }

    protected function matchLosses()
    {
        return $this->matchTotal() - $this->matchWins();
    }

    protected function gameTotal()
    {
        $ta_wins = $this->matches->sum('team_one_score');
        $tb_wins = $this->matches->sum('team_two_score');

        return $ta_wins + $tb_wins;
    }

    protected function gameWins()
    {
        $ta_wins = 0;
        foreach ($this->matches as $ta) {
            if ($ta->team_one == $this->id) {
                $ta_wins = $ta_wins + $ta->team_one_score;
            }
        }

        $tb_wins = 0;
        foreach ($this->matches as $tb) {
            if ($tb->team_two == $this->id) {
                $tb_wins = $tb_wins + $tb->team_two_score;
            }
        }

        $wins = $ta_wins + $tb_wins;

        return $wins;
    }

    protected function gameLosses()
    {
        return $this->gameTotal() - $this->gameWins();
    }
}
