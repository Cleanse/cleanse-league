<?php

namespace Cleanse\League\Classes\Stats;

/**
 * Class PlayerStats
 * @package Cleanse\League\Classes\Stats
 *
 * A class to cache the stats.
 */
class PlayerStats
{
    public $kills;
    public $kill_avg;
    public $deaths;
    public $death_avg;
    public $assists;
    public $assist_avg;
    public $damage;
    public $damage_avg;
    public $damage_per_second;
    public $healing;
    public $healing_avg;
    public $healing_per_second;
    public $medals;
    public $medals_avg;
    public $match_total;
    public $match_wins;
    public $match_losses;
    public $game_total;
    public $game_wins;
    public $game_win_avg;
    public $game_losses;
    public $game_loss_avg;
    public $jobs;
    public $jobs_avg;
    public $duration;
    public $duration_avg;

    public function __construct($player)
    {
        $this->kills = $player['kills'];
        $this->deaths = $player['deaths'];
        $this->assists = $player['assists'];
        $this->damage = $player['damage'];
        $this->healing = $player['healing'];
        $this->medals = $player['medals'];
        $this->match_total = $player['match_total'];
        $this->match_wins = $player['match_wins'];
        $this->match_losses = $player['match_losses'];
        $this->game_total = $player['game_total'];
        $this->game_wins = $player['game_wins'];
        $this->game_losses = $player['game_losses'];
        $this->duration = $player['duration'];
    }

    /**
     * @return array
     */
    public function seasonal()
    {
        return $this->buildArray();
    }

    /**
     * @return array
     */
    private function buildArray()
    {
        $stats = [
            'kills' => $this->kills,
            'kill_avg' => $this->average($this->kills),
            'deaths' => $this->deaths,
            'death_avg' => $this->average($this->deaths),
            'assists' => $this->assists,
            'assist_avg' => $this->average($this->assists),
            'damage' => $this->damage,
            'healing' => $this->healing,
            'medals' => $this->medals,
            'medals_avg' => $this->average($this->medals),
            'game_total' => $this->game_total,
            'game_wins' => $this->game_wins,
            'game_losses' => $this->game_losses,
            'match_total' => $this->match_total,
            'match_wins' => $this->match_wins,
            'match_losses' => $this->match_losses,
            'duration' => $this->duration,
            'duration_avg' => $this->averageTime($this->duration),
            'dps' => $this->perMinute($this->damage, $this->duration),
            'hps' => $this->perMinute($this->healing, $this->duration)
        ];

        return $stats;
    }

    private function average($stat)
    {
        if (!$this->game_total > 0) {
            return 0;
        }

        return round($stat / $this->game_total, 1);
    }

    private function averageTime($stat)
    {
        if (!$this->game_total > 0) {
            return 0;
        }

        $time = round($stat / $this->game_total, 0);

        return (int)round($time);
    }

    private function perMinute($stat, $seconds)
    {
        if (!$this->game_total > 0) {
            return 0;
        }

        return round($stat / $seconds, 2);
    }
}
