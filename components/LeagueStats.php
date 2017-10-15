<?php namespace Cleanse\League\Components;

use Cleanse\League\Models\Season;
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
        $this->addCss('assets/css/league.css');
        $this->addJs('assets/js/bootstrap-4-min.js');

        $this->initStatsData();
    }

    public function initStatsData()
    {
        $stats = $this->getStats();
        $this->page['damage'] = $stats->sortByDesc('damage')->take(3);
        $this->page['healing'] = $stats->sortByDesc('healing')->take(3);
        $this->page['medals'] = $stats->sortByDesc('medals')->take(3);
        $this->page['least'] = $stats->sortBy('medals')->take(3);
        $this->page['kills'] = $stats->sortByDesc('kills')->take(3);
        $this->page['deaths'] = $stats->sortByDesc('deaths')->take(3);
        $this->page['alive'] = $stats->sortBy('deaths')->take(3);
        $this->page['assists'] = $stats->sortByDesc('assists')->take(3);
    }

    protected function getStats()
    {
        $season = Season::orderBy('id', 'desc')
            ->first();

        //Get stats from this season.
        return EventPlayer::where('game_total', '>=', 1)
            ->where('playerable_type', '=', 'season')
            ->where('playerable_id', '=', $season->id)
            ->with('player')
            ->get();
    }
}
