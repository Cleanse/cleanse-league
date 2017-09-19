<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;
use Cleanse\League\Models\EventPlayer;

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
        $this->page['healing'] = $this->stats->sortByDesc('healing')->take(3);
        $this->page['medals'] = $this->stats->sortByDesc('medals')->take(3);
        $this->page['least'] = $this->stats->sortBy('medals')->take(3);
        $this->page['kills'] = $this->stats->sortByDesc('kills')->take(3);
        $this->page['deaths'] = $this->stats->sortByDesc('deaths')->take(3);
        $this->page['alive'] = $this->stats->sortBy('deaths')->take(3);
        $this->page['assists'] = $this->stats->sortByDesc('assists')->take(3);
    }

    protected function getStats()
    {
        //Get stats from this season.
        return EventPlayer::where('game_total', '>=', 1)
        ->with('player')->get();
    }
}
