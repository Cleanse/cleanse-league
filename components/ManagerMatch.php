<?php namespace Cleanse\League\Components;

use Flash;
use Input;
use Redirect;
use Session;
use Cms\Classes\ComponentBase;
use System\Models\File;
use Cleanse\League\Classes\MatchUpdater;
use Cleanse\League\Models\Match;
use Cleanse\League\Models\MatchGame;

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
        $this->addJs('assets/js/image-input.js');
        $this->page['flashSuccess'] = Session::get('flashSuccess');
        $this->page['match'] = $this->getMatch();
    }

    public function getMatch()
    {
        $match = $this->property('match');

        $this->page['jobs'] = $this->getXivJobs();

        return Match::with(['one.team', 'two.team', 'games' => function ($q) {
            $q->orderBy('created_at', 'asc');
        }])->find($match);
    }

    public function onCreateGame()
    {
        $post = Input::all();

        $createGame = new MatchUpdater;

        $game = $createGame->updateMatch($post);

        if (Input::hasFile('screenshot')) {
            $uploadedFile = Input::file('screenshot');

            $file = new File;
            $file->data = $uploadedFile;
            $file->is_public = true;
            $file->save();

            $game->screenshot()->add($file);
        }

        Flash::success('Game was added.');

        Session::flash('flashSuccess', true);

        return Redirect::refresh();
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
