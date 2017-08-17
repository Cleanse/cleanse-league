<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;
use Cleanse\League\Models\Team as TeamModel;

class Team extends ComponentBase
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
            'team' => [
                'title'       => 'Team unique id.',
                'description' => 'Team unique shortcode id.',
                'default'     => '{{ :team }}',
                'type'        => 'string',
            ],
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
        $teamId = $this->property('team');

        $team = TeamModel::whereId($teamId)
            ->with('event_teams.teamable')
            ->first();

        //dd($team->toArray());

        if (!$team || !$team->exists) {
            $this->setStatusCode(404);
            return $this->controller->run('404');
        }

        return $team;
    }
}
