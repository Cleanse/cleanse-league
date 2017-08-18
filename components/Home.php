<?php namespace Cleanse\League\Components;

use Log;
use Cms\Classes\ComponentBase;
use Cleanse\League\Models\League;
use Cleanse\League\Models\Championship;
use Cleanse\League\Models\Season;
use Cleanse\League\Models\Match;

use Cleanse\League\Models\EventTeam;

class Home extends ComponentBase
{
    public $league;
    public $championship;
    public $seasonal;
    public $season;

    public function componentDetails()
    {
        return [
            'name'        => 'League Home',
            'description' => 'League index page.'
        ];
    }

    public function onRun()
    {
        $this->addCss('/plugins/cleanse/league/assets/css/league.css');
        $this->league = $this->page['league'] = $this->getLeague(); //??

        $this->getCurrentHappening();
    }

    public function getLeague() //??
    {
        return League::first();
    }

    public function getCurrentHappening()
    {
        //Check if championship tourney is active
        $championshipFinals = Championship::whereHas('tourneys', function ($query) {
            $query->whereNull('winner_id');
        })
            ->with([
            'tourneys' => function ($query) {
                $query->whereNull('winner_id');
            }
        ])->first();

        if (isset($championshipFinals)) {
            $this->championship = $this->page['championship'] = $championshipFinals;
            return;
        }

        //Check if seasonal tourney is active
        $seasonFinals = Season::whereHas('tourneys', function ($query) {
            $query->whereNull('winner_id');
        })
            ->with([
            'tourneys' => function ($query) {
                $query->where('winner_id');
            }
        ])->first();

        if (isset($seasonFinals)) {
            $this->seasonal = $this->page['seasonal'] = $seasonFinals;
            return;
        }

        //Check if season has matches left
        $take = 4; //count($teams)/2
        $seasonMatches = Season::whereHas('matches', function ($query) {
            $query->whereNull('winner_id');
        })
            ->with([
            'matches' => function ($query) use ($take) {
                $query->whereNull('winner_id');
                $query->orderBy('takes_place_at', 'asc');
                $query->take($take);
                $query->with(['one' => function ($q) {
                    $q->with('team');
                }, 'two' => function ($q) {
                    $q->with('team');
                }]);
            }
        ])->first();

        if (isset($seasonMatches)) {
            $this->season = $this->page['season'] = $seasonMatches;
            return;
        }
    }
}
