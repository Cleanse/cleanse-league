<?php namespace Cleanse\League\Classes\Updaters;

use Cleanse\League\Models\Match;
use Cleanse\League\Models\EventPlayer;

class UpdaterEventPlayer
{
    protected $id;
    protected $matches;

    public function calculateStats($playerId)
    {
        $player = EventPlayer::find($playerId);

        $this->id = $player->id;

        $this->matches = Match::whereHas('players', function ($query) use ($player) {
            $query->where('player_id', $player->id);
        })
            ->with([
                'players' => function ($query) use ($player) {
                    $query->where('player_id', $player->id);

                }
            ])
            ->where(function ($query) use ($player) {
                $query->where('matchable_id', '=', $player->playerable_id)
                    ->where('matchable_type', '=', $player->playerable_type)
                    ->whereNotNull('winner_id');
            })
            ->get();

        $oldJobs = json_decode($player->jobs, true);

        $player->jobs = $this->jobsJson($oldJobs);
        $player->kills = $this->killsTotal();
        $player->deaths = $this->deathsTotal();
        $player->assists = $this->assistsTotal();
        $player->damage = $this->damageTotal();
        $player->healing = $this->healingTotal();
        $player->medals = $this->medalsTotal();
        $player->match_total = $this->matchTotal();
        $player->match_wins = $this->matchWins();
        $player->match_losses = $this->matchLosses();
        $player->game_total = $this->gameTotal();
        $player->game_wins = $this->gameWins();
        $player->game_losses = $this->gameLosses();

        $player->save();

    }

    protected function killsTotal()
    {
        $statTotal = 0;
        foreach ($this->matches as $match) {
            foreach ($match->players as $player) {
                $statTotal = $statTotal + $player->kills;
            }
        }

        return $statTotal;
    }

    protected function deathsTotal()
    {
        $statTotal = 0;
        foreach ($this->matches as $match) {
            foreach ($match->players as $player) {
                $statTotal = $statTotal + $player->deaths;
            }
        }

        return $statTotal;
    }

    protected function assistsTotal()
    {
        $statTotal = 0;
        foreach ($this->matches as $match) {
            foreach ($match->players as $player) {
                $statTotal = $statTotal + $player->assists;
            }
        }

        return $statTotal;
    }

    protected function damageTotal()
    {
        $statTotal = 0;
        foreach ($this->matches as $match) {
            foreach ($match->players as $player) {
                $statTotal = $statTotal + $player->damage;
            }
        }

        return $statTotal;
    }

    protected function healingTotal()
    {
        $statTotal = 0;
        foreach ($this->matches as $match) {
            foreach ($match->players as $player) {
                $statTotal = $statTotal + $player->healing;
            }
        }

        return $statTotal;
    }

    protected function medalsTotal()
    {
        $statTotal = 0;
        foreach ($this->matches as $match) {
            foreach ($match->players as $player) {
                $statTotal = $statTotal + $player->medals;
            }
        }

        return $statTotal;
    }

    protected function matchTotal()
    {
        return $this->matches->count();
    }

    protected function matchWins()
    {
        $matchWins = 0;
        foreach ($this->matches as $match) {
            if ($match->winner_id == $match->players[0]->team_id) {
                $matchWins = $matchWins + 1;
            }
        }


        return $matchWins;
    }

    protected function matchLosses()
    {
        return $this->matchTotal() - $this->matchWins();
    }

    protected function gameTotal()
    {
        $gamesTotal = 0;
        foreach ($this->matches as $match) {
            $gamesTotal = $gamesTotal + $match->players->count();
        }

        return $gamesTotal;
    }

    protected function gameWins()
    {
        $wins = 0;
        foreach ($this->matches as $match) {
            foreach ($match->players as $player) {
                if ($player->team_id == $player->game_winner_id) {
                    $wins = $wins + 1;
                }
            }
        }

        return $wins;
    }

    protected function gameLosses()
    {
        return $this->gameTotal() - $this->gameWins();
    }

    protected function jobsJson($oldJobs)
    {
        $jobsStats = [];

        foreach ($this->matches as $match) {
            foreach ($match->players as $player) {

                if ($player->player_job == '0') {
                    continue;
                }

                if ($player->player_job == '') {
                    continue;
                }

                if (!isset($jobsStats[$player->player_job])) {
                    $jobsStats[$player->player_job] = 1;
                    continue;
                }

                $jobsStats[$player->player_job]++;
            }
        }

        return json_encode($jobsStats);
    }
}
