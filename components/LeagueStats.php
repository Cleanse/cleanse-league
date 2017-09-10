<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;
use Cleanse\League\Models\MatchGamePlayer;

class LeagueStats extends ComponentBase
{
    public $stats;

    public function componentDetails()
    {
        return [
            'name'        => 'League Stats Page',
            'description' => 'Statistics page.',
        ];
    }

    public function onRun()
    {
        $this->addCss('/plugins/cleanse/league/assets/css/league.css');

        $this->initStatsData();
    }

    public function initStatsData()
    {
        $this->stats = $this->getStats();
        $this->page['damage'] = $this->stats->sortByDesc('damage')->take(3);
    }

    protected function getStats()
    {
        return MatchGamePlayer::with('player.player')->get();
    }
}
