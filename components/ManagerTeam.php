<?php namespace Cleanse\League\Components;

use Auth;
use Event;
use Flash;
use Input;
use Redirect;
use Session;
use Cms\Classes\ComponentBase;
use System\Models\File;
use Cleanse\League\Models\Team;

class ManagerTeam extends ComponentBase
{
    public $mode;

    public function componentDetails()
    {
        return [
            'name' => 'Lists League Teams to Manage',
            'description' => 'View a list of the league\'s teams.'
        ];
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->addJs('assets/js/bootstrap-4-min.js');
        $this->addJs('assets/js/image-input.js');

        $this->page['flashSuccess'] = Session::get('flashSuccess');
        $this->page['teams'] = $this->getTeamsList();
    }

    public function onUpdate()
    {
        $mode = post('mode');

        if (!$mode) {
            $mode = 'list';
        }

        $this->page['mode'] = $mode;

        if ($mode == 'list') {
            $this->page['teams'] = $this->getTeamsList();
        }
    }

    protected function getTeamsList()
    {
        return Team::orderBy('name', 'asc')
            ->paginate(20);
    }

    public function onCreateTeam()
    {
        $newTeam = new Team;
        $newTeam->name = post('name');

        if (post('initials') !== '') {
            $newTeam->initials = post('initials');
        }

        $newTeam->save();

        if (Input::hasFile('logo')) {
            $uploadedFile = Input::file('logo');

            $file = new File;
            $file->data = $uploadedFile;
            $file->is_public = true;
            $file->save();

            $newTeam->logo()->add($file);
        }

        Event::fire('cleanse.league',
            [Auth::getUser(), 'team.create', $newTeam]);

        Flash::success('Team was created.');

        Session::flash('flashSuccess', true);

        return Redirect::refresh();
    }

    public function getSearchFormAttributes()
    {
        $attributes = [];

        $attributes['method'] = 'POST';
        $attributes['data-request'] = $this->alias . '::onSearchForTeam';
        $attributes['data-request-update'] = "'" . $this->alias . "::search-results':'#team-list'";
        $attributes['class'] = 'justify-content-end';

        return $attributes;
    }

    public function onSearchForTeam()
    {
        $team = post('search');

        if (!$team) {
            throw new ValidationException(['name' => 'You must enter at least part of a team\'s name!']);
        }

        $list = Team::where('name', 'like', '%' . $team . '%')
            ->orderBy('name', 'desc')
            ->get();

        $this->page['items'] = $list;
    }
}
