<?php namespace Cleanse\League\Components;

use Flash;
use Input;
use Redirect;
use Session;
use Cms\Classes\ComponentBase;
use Cleanse\League\Models\Championship;
use Cleanse\League\Models\ChampionshipTeam;

class ManagerChampionshipPoints extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'League Manager: Championship Points',
            'description' => 'Manage a championship season\'s points.'
        ];
    }

    public function defineProperties()
    {
        return [
            'championship' => [
                'title' => 'Championship Season',
                'description' => 'The championship id.',
                'default' => '{{ :championship }}',
                'type' => 'string'
            ]
        ];
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->addJs('assets/js/bootstrap-4-min.js');

        $this->page['flashSuccess'] = Session::get('flashSuccess');

        $this->page['championship'] = $this->getTeamList();

        $this->page['standings'] = $this->getStandingsList();
    }

    private function getTeamList()
    {
        return $teams = Championship::orderBy('id', 'desc')
            ->with(['seasons' => function ($q) {
                $q->with('teams.team');
            }])
            ->first();
    }

    private function getStandingsList()
    {
        return ChampionshipTeam::orderBy('points', 'desc')
            ->where('championship_id', '=', $this->page['championship']->id)
            ->with('team')
            ->get();
    }

    public function onAddPoints()
    {
        //insert new data and reload
        //Create ChampTeam
        //Get ChampTeam id
        //Create PointSource with ChampTeam id

        Event::fire('cleanse.league',
            [Auth::getUser(), 'points.add', $editChampionship]);

        Flash::success('Points were added.');

        Session::flash('flashSuccess', true);

        return Redirect::refresh();
    }

    //edit a team for current season - edit sources
}