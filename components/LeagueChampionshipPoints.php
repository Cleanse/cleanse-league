<?php namespace Cleanse\League\Components;

use Cleanse\League\Models\ChampionshipTeam;
use Cms\Classes\ComponentBase;
use Cleanse\League\Models\Championship;

class LeagueChampionshipPoints extends ComponentBase
{
    public $championship;

    public function componentDetails()
    {
        return [
            'name'        => 'League Points Page',
            'description' => 'Championship season point standing\'s page.',
        ];
    }

    public function defineProperties()
    {
        return [
            'championship' => [
                'title' => 'Championship ID',
                'description' => 'The championship of the league.',
                'default' => '{{ :championship }}',
                'type' => 'string'
            ]
        ];
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->addJs('assets/js/bootstrap-4-min.js');

        if (!$this->getLeaguePointStandings()) {
            return redirect('/league');
        }

        return;
    }

    private function getLeaguePointStandings()
    {
        $this->page['championship'] = $championship = $this->getChampionship();

        if (!isset($championship)) {
            return false;
        }

        $this->page['teams'] = $this->getTeams($championship->id);

        return true;
    }

    /**
     * @return mixed
     */
    private function getChampionship()
    {
        if ($championship = $this->property('championship')) {
            return Championship::find($championship);
        }

        return Championship::orderBy('id', 'desc')
            ->first();
    }

    /**
     * @param $championshipId
     * @return mixed
     */
    private function getTeams($championshipId)
    {
        $teams = ChampionshipTeam::with(['team'])
            ->where('championship_id', '=', $championshipId)
            ->orderBy('points', 'desc')
            ->get();

        return $teams;
    }
}
