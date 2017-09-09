<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;
use Cleanse\League\Models\Team as TeamModel;

class LeagueTeam extends ComponentBase
{
    public $team;

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
        $this->addCss('/plugins/cleanse/league/assets/css/league.css');

        $this->team = $this->page['team'] = $this->getTeam();
    }

    public function getTeam()
    {
        $teamId = $this->property('slug');

        $team = TeamModel::whereSlug($teamId)
            ->with('event_teams.teamable')
            ->first();

        if (!$team || !$team->exists) {
            $this->setStatusCode(404);
            return $this->controller->run('404');
        }

        dd($team->toArray());

        return $team;
    }
}
