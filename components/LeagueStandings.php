<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;
use Cleanse\League\Models\EventTeam;

class LeagueStandings extends ComponentBase
{
    public $teams;

    public function componentDetails()
    {
        return [
            'name'        => 'League Standings Page',
            'description' => 'Seasonal standings page.',
        ];
    }

    public function onRun()
    {
        $this->addCss('/plugins/cleanse/league/assets/css/league.css');

        $this->teams = $this->page['teams'] = $this->getTeams();
    }

    public function getTeams()
    {
        //Needs to get current season team, not all.
        $teams = EventTeam::with('team')
            ->orderBy('match_wins', 'desc')
            ->get();

        return $teams;
    }
}
