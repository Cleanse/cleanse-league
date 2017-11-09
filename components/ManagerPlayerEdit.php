<?php namespace Cleanse\League\Components;

use Auth;
use Event;
use Flash;
use Redirect;
use Session;
use Cms\Classes\ComponentBase;
use Cleanse\League\Models\Player;

class ManagerPlayerEdit extends ComponentBase
{
    public $mode;
    public $model;

    public function componentDetails()
    {
        return [
            'name' => 'Manage League Players',
            'description' => 'Manage a league player.'
        ];
    }

    /**
     * Uploader inject
     */
    public function init()
    {
        $player_id = $this->property('player');

        $this->model = Player::find($player_id);

        $component = $this->addComponent(
            'Cleanse\Uploader\Components\ImageUploader',
            'cleanseImageUploader',
            ['deferredBinding' => false]
        );

        $component->bindModel('avatar', $this->model);
    }

    public function onRefreshFiles()
    {
        $this->pageCycle();
    }

    public function defineProperties()
    {
        return [
            'player' => [
                'title' => 'League Player',
                'description' => 'The player id.',
                'default' => '{{ :player }}',
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
        $this->page['player'] = $this->model;
    }

    public function onEditPlayer()
    {
        $editPlayer = Player::find(post('player_id'));

        $editPlayer->name = post('name');

        $editPlayer->save();

        Event::fire('cleanse.league',
            [Auth::getUser(), 'player.edit', $editPlayer]);

        Flash::success('Player: ' . $editPlayer->name . ' was edited.');
        Session::flash('flashSuccess', true);

        return Redirect::to('/manager/player');
    }
}
