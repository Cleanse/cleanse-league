<?php namespace Cleanse\League\Components;

use Flash;
use Input;
use Redirect;
use Session;
use Cms\Classes\ComponentBase;
use System\Models\File;
use Cleanse\League\Models\Player;

class ManagerPlayer extends ComponentBase
{
    public $mode;
    public $model;

    public function componentDetails()
    {
        return [
            'name' => 'Manage League Players',
            'description' => 'Manage the league\'s players.'
        ];
    }

    /**
     * Uploader inject
     */
    public function init()
    {
        if (!isset($this->model)) {
            $player = Player::first(['id']);

            if (!$player) {
                return;
            }

            $this->model = $player->id;
        }

        $model = $this->model;

        $component = $this->addComponent(
            'Cleanse\Uploader\Components\ImageUploader',
            'cleanseImageUploader',
            ['deferredBinding' => false]
        );

        $component->bindModel('avatar', Player::find($model));
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->page['flashSuccess'] = Session::get('flashSuccess');
        $this->page['players'] = $this->getPlayersList();
    }

    public function onUpdate()
    {
        $mode = post('mode');

        if (!$mode) {
            $this->page['mode'] = 'list';
        }

        $this->page['mode'] = $mode;

        if ($mode == 'edit') {
            $this->model = post('player_id');

            $this->init();

            $this->page['player'] = Player::find($this->model);
        }

        if ($mode == 'list') {
            $this->page['players'] = $this->getPlayersList();
        }
    }

    protected function getPlayersList()
    {
        return Player::orderBy('name', 'asc')
            ->paginate(20);
    }

    public function onCreatePlayer()
    {
        $newPlayer = new Player();
        $newPlayer->name = post('name');

        $newPlayer->save();

        if (Input::hasFile('avatar')) {
            $uploadedFile = Input::file('avatar');

            $file = new File;
            $file->data = $uploadedFile;
            $file->is_public = true;
            $file->save();

            $newPlayer->avatar()->add($file);
        }

        Flash::success('Player ' . $newPlayer->name . ' was created.');

        Session::flash('flashSuccess', true);

        return Redirect::refresh();
    }

    public function onEditPlayer()
    {
        $editPlayer = Player::find(post('player_id'));

        $editPlayer->name = post('name');

        $editPlayer->save();

        Flash::success('Player ' . $editPlayer->name . ' was edited.');

        Session::flash('flashSuccess', true);

        return Redirect::refresh();
    }

    /**
     * @return array
     */
    public function getSearchFormAttributes()
    {
        $attributes = [];

        $attributes['method'] = 'POST';
        $attributes['data-request'] = $this->alias . '::onSearchForPlayer';
        $attributes['data-request-update'] = "'" . $this->alias . "::players':'#team-list'";
        $attributes['class'] = 'justify-content-end';

        return $attributes;
    }

    public function onSearchForPlayer()
    {
        $player = post('search');

        if (!$player) {
            throw new ValidationException(['name' => 'You must enter at least part of a player\'s name!']);
        }

        $list = Player::where('name', 'like', '%' . $player . '%')
            ->orderBy('name', 'desc')
            ->get();

        $this->page['items'] = $list;
    }
}