<?php namespace Cleanse\League\Components;

use Flash;
use Input;
use Redirect;
use Request;
use Session;
use Cms\Classes\ComponentBase;
use Cleanse\League\Models\Team;

class ManagerTeam extends ComponentBase
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
        if (!isset($this->model)) {
            $team = Team::first(['id']);

            $this->model = $team->id;
        }

        $model = $this->model;

        $component = $this->addComponent(
            'Cleanse\Uploader\Components\ImageUploader',
            'cleanseImageUploader',
            ['deferredBinding' => false]
        );

        $component->bindModel('logo', Team::find($model));
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->page['flashSuccess'] = Session::get('flashSuccess');
        $this->page['teams'] = $this->getTeamsList();
    }

    public function onUpdate()
    {
        $mode = post('mode', 'list');

        if (!$mode) {
            $this->page['mode'] = 'list';
        }

        $this->page['mode'] = $mode;

        //if ($mode == 'create')

        if ($mode == 'edit') {
            $this->model = post('team_id');
            $this->page['team'] = Team::find($this->model);
        }

        if ($mode == 'list') {
            $this->page['teams'] = $this->getTeamsList();
        }
    }

    public function getTeamsList()
    {
        return Team::paginate(20);
    }

    /**
     * @return array
     */
    public function getCreateFormAttributes()
    {
        $attributes = [];

        $attributes['method'] = 'POST';
        $attributes['data-request'] = $this->alias . '::onCreateTeam';
        $attributes['data-request-update'] = "'" . $this->alias . "::flash-message':'#cleanse-league-form-message','"
            . $this->alias . "::success':'#cleanse-league-form'";
        $attributes['data-request-confirm'] = 'Is the information correct?';

        return $attributes;
    }

    public function onCreateTeam()
    {
        //
    }

    public function onEditTeam()
    {
        //
    }
}