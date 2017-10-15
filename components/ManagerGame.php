<?php namespace Cleanse\League\Components;

use Auth;
use Event;
use Input;
use Flash;
use Redirect;
use Session;
use Cms\Classes\ComponentBase;
use Cleanse\League\Classes\Updaters\UpdaterMatchGamePlayer;
use Cleanse\League\Models\MatchGame;
use Cleanse\League\Models\MatchGamePlayer;

class ManagerGame extends ComponentBase
{
    public $model;

    public function componentDetails()
    {
        return [
            'name' => 'League Manager: Game',
            'description' => 'Edit a league game.'
        ];
    }

    public function init()
    {
        $game_id = $this->property('game');

        $this->model = $this->getGame($game_id);

        if (!$this->model) {
            return 'No model.';
        }

        $component = $this->addComponent(
            'Cleanse\Uploader\Components\ImageUploader',
            'cleanseImageUploader',
            ['deferredBinding' => false]
        );

        $component->bindModel('screenshot', $this->model);
    }

    public function onRefreshFiles()
    {
        $this->pageCycle();
    }

    public function defineProperties()
    {
        return [
            'game' => [
                'title' => 'League Game',
                'description' => 'The game id.',
                'default' => '{{ :game }}',
                'type' => 'string'
            ]
        ];
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->addJs('assets/js/bootstrap-4-min.js');

        $this->page['flashSuccess'] = Session::get('flashSuccess');

        if (!$this->model) {
            Flash::error('Game doesn\'t exist.');

            Session::flash('flashSuccess', true);

            return Redirect::to('/manager/match');
        }

        $this->initGame();
    }

    public function initGame()
    {
        $this->page['game'] = $this->model;
        $this->page['players'] = $this->getGamePlayers();
        $this->page['jobs'] = $this->getXivJobs();
    }

    public function onEditGame()
    {
        $post = Input::all();

        $game = new UpdaterMatchGamePlayer;

        $game->update($post);

        Flash::success('Game was edited.');

        Session::flash('flashSuccess', true);

        return Redirect::refresh();
    }

    public function onDeleteGame()
    {
        $post = Input::all();

        $game = new UpdaterMatchGamePlayer;

        $game->delete($post);

        Flash::success('Game was deleted.');

        Session::flash('flashSuccess', true);

        return Redirect::to('/manager/match/' . $post['match_id']);
    }

    protected function getGame($id)
    {
        return MatchGame::with(['one.team.players', 'two.team.players', 'winner.team'])->find($id);
    }

    protected function getGamePlayers()
    {
        return MatchGamePlayer::where('game_id', '=', $this->model->id)->get();
    }

    protected function getXivJobs()
    {
        return [
            ['abbr' => 'drk', 'name' => 'Dark Knight'],
            ['abbr' => 'pld', 'name' => 'Paladin'],
            ['abbr' => 'war', 'name' => 'Warrior'],
            ['abbr' => 'ast', 'name' => 'Astrologian'],
            ['abbr' => 'sch', 'name' => 'Scholar'],
            ['abbr' => 'whm', 'name' => 'White Mage'],
            ['abbr' => 'brd', 'name' => 'Bard'],
            ['abbr' => 'mch', 'name' => 'Machinist'],
            ['abbr' => 'blm', 'name' => 'Black Mage'],
            ['abbr' => 'rdm', 'name' => 'Red Mage'],
            ['abbr' => 'smn', 'name' => 'Summoner'],
            ['abbr' => 'drg', 'name' => 'Dragoon'],
            ['abbr' => 'mnk', 'name' => 'Monk'],
            ['abbr' => 'nin', 'name' => 'Ninja'],
            ['abbr' => 'sam', 'name' => 'Samurai']
        ];
    }
}