<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;
use Cleanse\League\Models\Season;

class LeagueSchedule extends ComponentBase
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
        $this->addCss('assets/css/league.css');
        $this->addJs('assets/js/bootstrap-4-min.js');

        $this->getSchedule();
    }

    public function getSchedule()
    {
        $getSeason = $this->getSeason();

        if (!isset($getSeason)) {
            return;
        }

        $this->season = $this->page['season'] = $getSeason;

        $this->page['matches'] = $this->getMatches();
        $this->page['teams'] = $this->season->teams;
    }

    public function getSeason()
    {
        return Season::whereHas('matches', function ($query) {
            if (!$slug = $this->property('season')) {
                $query->whereNull('winner_id');
            } else {
                $query->whereSlug($slug);
            }
        })
            ->with([
                'matches' => function ($query) {
                    $query->orderBy('takes_place_during', 'asc');
                    $query->with(['one.team', 'two.team']);
                },
                'teams.team'])->first();
    }

    public function getMatches()
    {
        $matches = $this->season->matches->groupBy('takes_place_during');

        if ($week = $this->property('week')) {
            $matches = $matches->slice($week - 1, 1);
        }

        return $matches;
    }
}
