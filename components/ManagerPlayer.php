<?php namespace Cleanse\League\Components;

use Auth;
use Event;
use Flash;
use Redirect;
use Session;
use Cms\Classes\ComponentBase;
use Cleanse\League\Models\Player;

class ManagerPlayer extends ComponentBase
{
    public $model;

    public function componentDetails()
    {
        return [
            'name' => 'Manage League Players',
            'description' => 'Manage the league\'s players.'
        ];
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->addJs('assets/js/bootstrap-4-min.js');

        $this->page['flashSuccess'] = Session::get('flashSuccess');
        $this->page['players'] = $this->getPlayersList();
    }

    public function onUpdate()
    {
        $mode = post('mode');

        if (!$mode) {
            $mode = 'list';
        }

        $this->page['mode'] = $mode;

        if ($mode == 'edit') {
            $this->model = post('player_id');

            $this->page['player'] = Player::find($this->model);
        }

        if ($mode == 'list') {
            $this->page['players'] = $this->getPlayersList();
        }
    }

    protected function getPlayersList()
    {
        return Player::orderBy('name', 'asc')
            ->paginate(50);
    }

    public function onCreatePlayer()
    {
        $newPlayer = new Player();
        $newPlayer->name = post('name');

        $newPlayer->save();

        Event::fire('cleanse.league',
            [Auth::getUser(), 'player.create', $newPlayer]);

        Flash::success('Player ' . $newPlayer->name . ' was created.');

        Session::flash('flashSuccess', true);

        return Redirect::refresh();
    }

    public function onEditPlayer()
    {
        $editPlayer = Player::find(post('player_id'));

        $editPlayer->name = post('name');

        $editPlayer->save();

        Event::fire('cleanse.league',
            [Auth::getUser(), 'player.edit', $editPlayer]);

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
        $attributes['data-request-update'] = "'" . $this->alias . "::search-results':'#team-list'";
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