<?php namespace Cleanse\League\Components;

use Auth;
use Event;
use Flash;
use Redirect;
use Session;
use Cms\Classes\ComponentBase;
use Cleanse\League\Models\Team;
use Cleanse\League\Models\Player;

class ManagerTeamEdit extends ComponentBase
{
    public $mode;
    public $model;

    public function componentDetails()
    {
        return [
            'name' => 'Manage League Teams',
            'description' => 'Manage the league\'s teams.'
        ];
    }

    /**
     * Uploader inject
     */
    public function init()
    {
        $team_id = $this->property('team');

        $this->model = Team::find($team_id);

        $component = $this->addComponent(
            'Cleanse\Uploader\Components\ImageUploader',
            'cleanseImageUploader',
            ['deferredBinding' => false]
        );

        $component->bindModel('logo', $this->model);
    }

    public function onRefreshFiles()
    {
        $this->pageCycle();
    }

    public function defineProperties()
    {
        return [
            'team' => [
                'title' => 'League Team',
                'description' => 'The team id.',
                'default' => '{{ :team }}',
                'type' => 'string'
            ]
        ];
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->addJs('assets/js/bootstrap-4-min.js');

        $this->page['flashSuccess'] = Session::get('flashSuccess');
        $this->setupPage();
    }

    protected function setupPage()
    {
        $this->page['team'] = $this->model;
        $this->page['players'] = $this->getFreeAgentPlayers();
        $this->page['roster'] = $this->getTeamRoster();
    }

    public function onUpdate()
    {
        $mode = post('mode');

        if (!$mode) {
            $mode = 'players';
        }

        $this->page['mode'] = $mode;

        if ($mode == 'players') {
            $this->page['teams'] = $this->setupPage();
        }
    }

    public function onEditTeam()
    {
        $editTeam = Team::find(post('team_id'));

        $editTeam->name = post('name');
        $editTeam->initials = post('initials');

        $editTeam->save();

        Event::fire('cleanse.league',
            [Auth::getUser(), 'team.edit', $editTeam]);

        Flash::success('Team ' . $editTeam->name . ' was edited.');
        Session::flash('flashSuccess', true);

        return Redirect::to('/manager/team');
    }

    public function getFreeAgentPlayers()
    {
        return $freeAgents = Player::whereNull('team_id')->get();
    }

    public function getTeamRoster()
    {
        return $roster = Player::where('team_id', '=', $this->model->id)->get();
    }

    public function onAddPlayer()
    {
        $pId = post('player_id');
        $teamId = post('team_id');

        $editPlayer = Player::find($pId);

        $editPlayer->team_id = $teamId;

        $editPlayer->save();

        Event::fire('cleanse.league',
            [Auth::getUser(), 'team.player.add', $editPlayer]);

        $this->setupPage();
    }

    public function onReleasePlayer()
    {
        $pId = post('player_id');

        $editPlayer = Player::find($pId);

        $editPlayer->team_id = null;

        $editPlayer->save();

        Event::fire('cleanse.league',
            [Auth::getUser(), 'team.player.release', $editPlayer]);

        $this->setupPage();
    }

    public function getSearchFormAttributes()
    {
        $attributes = [];

        $attributes['method'] = 'POST';
        $attributes['data-request'] = $this->alias . '::onSearchForPlayer';
        $attributes['data-request-update'] = "'" . $this->alias . "::search-results':'#player-list'";
        $attributes['class'] = 'justify-content-end';

        return $attributes;
    }

    public function onSearchForPlayer()
    {
        $search = post('search');

        if (!$search) {
            throw new ValidationException(['name' => 'You must enter at least part of a team\'s name!']);
        }

        $freeAgents = Player::whereNull('team_id')
            ->where('name', 'like', '%' . $search . '%')
            ->get();

        $this->page['team'] = $this->model;
        $this->page['players'] = $freeAgents;
    }
}
