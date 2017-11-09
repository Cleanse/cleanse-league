<?php namespace Cleanse\League\Classes\Stats;

class TeamStats
{
    public $kills;
    public $deaths;
    public $assists;
    public $damage;
    public $healing;
    public $medals;
    public $match_total;
    public $match_wins;
    public $match_losses;
    public $game_total;
    public $game_wins;
    public $game_losses;
    public $jobs;

    public function __construct($player)
    {
//        $this->kills = $player['kills'];
//        $this->deaths = $player['deaths'];
//        $this->assists = $player['assists'];
//        $this->damage = $player['damage'];
//        $this->healing = $player['healing'];
//        $this->medals = $player['medals'];
//        $this->match_total = $player['match_total'];
//        $this->match_wins = $player['match_wins'];
//        $this->match_losses = $player['match_losses'];
//        $this->game_total = $player['game_total'];
//        $this->game_wins = $player['game_wins'];
//        $this->game_losses = $player['game_losses'];
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
        return ['kpg' => 2.3, 'dpg' => 0.3, 'apg' => 3.3, 'wins' => 98, 'losses' => 2, 'winPercentage' => 98];

        return [
            'kills' => $this->kills,
            'deaths' => $this->deaths,
            'assists' => $this->assists,
            'kill_avg' => $this->average($this->kills),
            'death_avg' => $this->average($this->deaths),
            'assist_avg' => $this->average($this->assists),
            'damage' => $this->damage,
            'healing' => $this->healing,
            'damage_avg' => $this->average($this->damage),
            'healing_avg' => $this->average($this->healing),
            'medals' => $this->medals,
            'medals_avg' => $this->average($this->medals),
            'game_total' => $this->game_total,
            'game_wins' => $this->game_wins,
            'game_losses' => $this->game_losses,
            'match_total' => $this->match_total,
            'match_wins' => $this->match_wins,
            'match_losses' => $this->match_losses
        ];
    }

    private function average($stat)
    {
        if (!$this->game_total > 0) {
            return 0;
        }

        return round($stat / $this->game_total, 1);
    }
}
