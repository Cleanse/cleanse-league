<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;
use Cleanse\League\Models\Season;

class SeasonSchedule extends ComponentBase
{
    public $season;
    public $matches;
    public $teams;

    public function componentDetails()
    {
        return [
            'name' => 'League Schedule',
            'description' => 'League schedule component.'
        ];
    }

    public function defineProperties()
    {
        return [
            'season' => [
                'title' => 'League Season',
                'description' => 'The season of the league.',
                'default' => '{{ :season }}',
                'type' => 'string'
            ],
            'week' => [
                'title' => 'League Week',
                'description' => 'Look up the week by slug.',
                'default' => '{{ :week }}',
                'type' => 'string'
            ]
        ];
    }

    public function onRun()
    {
        $this->addCss('/plugins/cleanse/league/assets/css/league.css');

        $this->getSeason();
        $this->getMatches();
        $this->getTeams();
    }

    public function getSeason()
    {
        $seasonMatches = Season::whereHas('matches', function ($query) {
            if (!$slug = $this->property('season')) {
                $query->whereNull('winner_id');
            } else {
                $query->whereSlug($slug);
            }
        })
            ->with([
                'matches' => function ($query) {
                    $query->orderBy('takes_place_at', 'asc');
                    $query->with(['one' => function ($q) {
                        $q->with('team');
                    }, 'two' => function ($q) {
                        $q->with('team');
                    }]);
                },
                'teams' => function ($query) {
                    $query->with('team');
                }
            ])->first();

        if (isset($seasonMatches)) {
            $this->season = $this->page['season'] = $seasonMatches;
        }
    }

    public function getMatches()
    {
        $this->matches = $this->season->matches->groupBy('takes_place_at');

        if ($week = $this->property('week')) {
            $this->matches = $this->matches->slice($week - 1, 1);
        }

        $this->page['matches'] = $this->matches;
    }

    public function getTeams()
    {
        $this->teams = $this->page['teams'] = $this->season->teams;
    }
}