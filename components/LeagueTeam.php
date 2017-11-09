<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;
use Cleanse\League\Models\Team as TeamModel;

class LeagueTeam extends ComponentBase
{
    public $team;
    public $roster;

    public function componentDetails()
    {
        return [
            'name'        => 'League Team Page',
            'description' => 'Individual team page.',
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'Team Slug',
                'description' => 'Team slug.',
                'default'     => '{{ :slug }}',
                'type'        => 'string',
            ]
        ];
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->addJs('assets/js/bootstrap-4-min.js');

        $this->setupLeagueTeam();
    }

    private function setupLeagueTeam()
    {
        $team = $this->getTeam();

        if (!isset($team)) {
            return redirect('/league');
        }

        return $this->page['team'] = $team;
    }

    public function getTeam()
    {
        $teamId = $this->property('slug');

        $team = TeamModel::whereSlug($teamId)
            ->with(['event_teams.teamable', 'players'])
            ->first();

        return $team;
    }
}
