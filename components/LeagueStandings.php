<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;
use Cleanse\League\Models\Season;
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

    public function defineProperties()
    {
        return [
            'season' => [
                'title' => 'League Season',
                'description' => 'The season of the league.',
                'default' => '{{ :season }}',
                'type' => 'string'
            ]
        ];
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->addJs('assets/js/bootstrap-4-min.js');

        if (!$this->getLeagueStandings()) {
            return redirect('/league');
        }

        return;
    }

    public function getLeagueStandings()
    {
        $this->page['season'] = $season = $this->getSeason();

        if (!isset($season)) {
            return false;
        }

        $this->page['teams'] = $this->getTeams($season->id);

        return true;
    }

    /**
     * @return mixed
     */
    private function getSeason()
    {
        if ($season = $this->property('season')) {
            return Season::find($season);
        }

        return Season::orderBy('id', 'desc')
            ->first();
    }

    /**
     * @param $season
     * @return mixed
     */
    public function getTeams($season)
    {
        $teams = EventTeam::with('team')
            ->where('teamable_type', '=', 'season')
            ->where('teamable_id', '=', $season)
            ->orderBy('match_wins', 'desc')
            ->get();

        return $teams;
    }
}
