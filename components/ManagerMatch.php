<?php namespace Cleanse\League\Components;

use Auth;
use Flash;
use Input;
use Redirect;
use Session;
use Cms\Classes\ComponentBase;
use System\Models\File;
use Cleanse\League\Classes\ManagerLog\LeagueHandler;
use Cleanse\League\Classes\Updaters\UpdaterMatchGame;
use Cleanse\League\Models\Match;

class ManagerMatch extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'League Manager: Match',
            'description' => 'Edit a league match.'
        ];
    }

    public function defineProperties()
    {
        return [
            'match' => [
                'title' => 'League Match',
                'description' => 'The match id.',
                'default' => '{{ :match }}',
                'type' => 'string'
            ]
        ];
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->addJs('assets/js/bootstrap-4-min.js');
        $this->addJs('assets/js/image-input.js');

        $this->page['flashSuccess'] = Session::get('flashSuccess');
        $this->initMatch();
    }

    public function initMatch()
    {
        $this->page['match'] = $this->getMatch();
        $this->page['jobs'] = $this->getXivJobs();
    }

    public function onCreateGame()
    {
        $post = Input::all();

        $match = new UpdaterMatchGame;

        $matchGame = $match->update($post);

        if (Input::hasFile('screenshot')) {
            $uploadedFile = Input::file('screenshot');

            $file = new File;
            $file->data = $uploadedFile;
            $file->is_public = true;
            $file->save();

            $matchGame->screenshot()->add($file);
        }

        $log = new LeagueHandler();
        $log->handle(Auth::getUser(), 'match.game.create', $matchGame);

        Flash::success('Game was added.');

        Session::flash('flashSuccess', true);

        return Redirect::refresh();
    }

    public function onFinalizeMatch()
    {
        $post = Input::all();

        $match = new UpdaterMatchGame;

        $winner = $match->finalize($post);

        Flash::success('Match was finalized, ' . $winner . ' was victorious.');

        Session::flash('flashSuccess', true);

        return Redirect::refresh();
    }

    public function onUnlockMatch()
    {
        $post = Input::all();

        $match = new UpdaterMatchGame;

        $match->unlock($post);

        Flash::success('Match was unlocked. You can add / remove games again.');

        Session::flash('flashSuccess', true);

        return Redirect::refresh();
    }

    protected function getMatch()
    {
        $match = $this->property('match');

        return Match::with(['one.team.players', 'two.team.players', 'games' => function ($q) {
            $q->orderBy('created_at', 'asc');
        }])->find($match);
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
