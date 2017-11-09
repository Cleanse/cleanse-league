<?php namespace Cleanse\League\Components;

use Auth;
use DB;
use Event;
use Flash;
use Input;
use Redirect;
use Session;
use Cms\Classes\ComponentBase;
use Cleanse\League\Classes\Scheduler\SeasonScheduler as Scheduler;
use Cleanse\League\Models\Championship;
use Cleanse\League\Models\EventTeam;
use Cleanse\League\Models\Season;
use Cleanse\League\Models\Team;

class ManagerSeason extends ComponentBase
{
    public $model;
    public $post;

    public function componentDetails()
    {
        return [
            'name' => 'League Manager: Season',
            'description' => 'Manage the league\'s seasonal related actions.'
        ];
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->addJs('assets/js/bootstrap-4-min.js');

        $this->page['flashSuccess'] = Session::get('flashSuccess');
        $this->page['seasons'] = $this->getSeasonsList();
    }

    public function onUpdate()
    {
        $mode = post('mode');

        if (!$mode) {
            $mode = 'list';
        }

        $this->page['mode'] = $mode;

        if ($mode == 'list') {
            $this->page['seasons'] = $this->getSeasonsList();
        }

        if ($mode == 'create') {
            return $this->modeCreate();
        }

        if ($mode == 'edit') {
            return $this->modeEdit();
        }

        if ($mode == 'teams') {
            return $this->modeTeams();
        }

        if ($mode == 'schedule') {
            return $this->modeSchedule();
        }
    }

    protected function modeCreate()
    {
        $check = Season::whereNull('winner_id')->first();

        if ($check) {
            Flash::error('Season: "' . $check->name . '" has not completed yet.');

            Session::flash('flashSuccess', true);

            return Redirect::refresh();
        }

        $championships = Championship::orderBy('id', 'desc')->get();

        if (!$championships->count() > 0) {
            Flash::error('Before creating a season, create a championship.');

            Session::flash('flashSuccess', true);

            return Redirect::to('/manager/championship');
        }

        $this->page['championships'] = $championships;
    }

    public function onCreateSeason()
    {
        $cId = post('championship');
        $seasonName = post('name');

        $championship = Championship::find($cId);

        $season = $championship->seasons()->create([
            'name' => $seasonName
        ]);

        Event::fire('cleanse.league',
            [Auth::getUser(), 'season.create', $season]);

        Flash::success('Season: "' . $season->name . '" was created.');

        Session::flash('flashSuccess', true);

        return Redirect::refresh();
    }

    protected function modeEdit()
    {
        $this->model = post('season_id');

        $this->page['season'] = Season::find($this->model);
    }

    public function onEditSeason()
    {
        $editSeason = Season::find(post('season_id'));

        $editSeason->name = post('name');

        $editSeason->save();

        Event::fire('cleanse.league',
            [Auth::getUser(), 'season.edit', $editSeason]);

        Flash::success('Season ' . $editSeason->name . ' was edited.');

        Session::flash('flashSuccess', true);

        return Redirect::refresh();
    }

    public function onDeleteSeason()
    {
        $deleteSeason = Season::find(post('season_id'));

        $deleteSeasonCheck = $deleteSeason->teams->count();

        if ($deleteSeasonCheck > 0) {
            Flash::error('Season ' . $deleteSeason->name . ' cannot be deleted.');

            Session::flash('flashSuccess', true);

            return Redirect::refresh();
        }

        $deleteSeason->delete();

        Event::fire('cleanse.league',
            [Auth::getUser(), 'season.delete', $deleteSeason]);

        Flash::success('Season ' . $deleteSeason->name . ' was deleted.');

        Session::flash('flashSuccess', true);

        return Redirect::refresh();
    }

    protected function modeTeams()
    {
        $check = Season::whereNull('winner_id')
            ->has('matches')
            ->first();

        if ($check) {
            Flash::error('Season: "' . $check->name . '" has a locked roster.');

            Session::flash('flashSuccess', true);

            return Redirect::refresh();
        }

        $season = Season::with('teams.team')->whereNull('winner_id')->first();

        if (!$season) {
            Flash::error('There is no season, create a season. You also may need to create a championship still.');

            Session::flash('flashSuccess', true);

            return Redirect::refresh();
        }

        $seasonTeams = DB::table('cleanse_league_event_teams')
            ->join('cleanse_league_teams', 'cleanse_league_event_teams.team_id', '=', 'cleanse_league_teams.id')
            ->select('cleanse_league_event_teams.id', 'cleanse_league_event_teams.team_id', 'cleanse_league_teams.name')
            ->where('cleanse_league_event_teams.teamable_type', 'season')
            ->where('cleanse_league_event_teams.teamable_id', $season->id)
            ->orderBy('cleanse_league_teams.name', 'asc')
            ->get();

        $idVals = [];
        foreach ($seasonTeams as $team) {
            $idVals[] = $team->team_id;
        }

        $teams = Team::whereNotIn('id', $idVals)->orderBy('name', 'asc')->get();

        $this->page['teams'] = $teams;
        $this->page['eteams'] = $seasonTeams;
        $this->page['season'] = $season->id;
    }

    public function onAddTeam()
    {
        $team_id = post('team_id');
        $season_id = post('season_id');

        $season = Season::with('teams')->find($season_id);
        $createdTeam = $season->teams()->create([
            'team_id' => $team_id,
        ]);

        Event::fire('cleanse.league',
            [Auth::getUser(), 'season.team.add', $createdTeam]);

        $seasonTeams = DB::table('cleanse_league_event_teams')
            ->join('cleanse_league_teams', 'cleanse_league_event_teams.team_id', '=', 'cleanse_league_teams.id')
            ->select('cleanse_league_event_teams.id', 'cleanse_league_event_teams.team_id', 'cleanse_league_teams.name')
            ->where('cleanse_league_event_teams.teamable_type', 'season')
            ->where('cleanse_league_event_teams.teamable_id', $season_id)
            ->orderBy('cleanse_league_teams.name', 'asc')
            ->get();

        $idVals = [];
        foreach ($seasonTeams as $team) {
            $idVals[] = $team->team_id;
        }

        $teams = Team::whereNotIn('id', $idVals)->orderBy('name', 'asc')->get();

        $this->page['mode'] = 'teams';
        $this->page['season'] = $season_id;
        $this->page['teams'] = $teams;
        $this->page['eteams'] = $seasonTeams;
    }

    public function getCreateTeamFormAttributes()
    {
        $attributes = [];

        $attributes['method'] = 'POST';
        $attributes['data-request'] = $this->alias . '::onCreateTeam';
        $attributes['data-request-update'] = "'" . $this->alias . "::teams':'#editor-area'";

        return $attributes;
    }

    public function onCreateTeam()
    {
        $newTeam = new Team;
        $newTeam->name = post('name');

        if (post('initials') !== '') {
            $newTeam->initials = post('initials');
        }

        $newTeam->save();

        Event::fire('cleanse.league',
            [Auth::getUser(), 'team.create.season', $newTeam]);

        $team_id = $newTeam->id;
        $season_id = post('season_id');

        $season = Season::with('teams')->find($season_id);
        $createTeam = $season->teams()->create([
            'team_id' => $team_id,
        ]);

        Event::fire('cleanse.league',
            [Auth::getUser(), 'season.team.add', $createTeam]);

        $seasonTeams = DB::table('cleanse_league_event_teams')
            ->join('cleanse_league_teams', 'cleanse_league_event_teams.team_id', '=', 'cleanse_league_teams.id')
            ->select('cleanse_league_event_teams.id', 'cleanse_league_event_teams.team_id', 'cleanse_league_teams.name')
            ->where('cleanse_league_event_teams.teamable_type', 'season')
            ->where('cleanse_league_event_teams.teamable_id', $season_id)
            ->orderBy('cleanse_league_teams.name', 'asc')
            ->get();

        $idVals = [];
        foreach ($seasonTeams as $team) {
            $idVals[] = $team->team_id;
        }

        $teams = Team::whereNotIn('id', $idVals)->orderBy('name', 'asc')->get();

        $this->page['mode'] = 'teams';
        $this->page['season'] = $season_id;
        $this->page['teams'] = $teams;
        $this->page['eteams'] = $seasonTeams;
    }

    public function onRemoveTeam()
    {
        $team_id = post('team_id');
        $season_id = post('season_id');

        $deleteTeam = EventTeam::find($team_id);

        $deleteTeam->delete();

        Event::fire('cleanse.league',
            [Auth::getUser(), 'season.team.delete', $deleteTeam]);

        $seasonTeams = DB::table('cleanse_league_event_teams')
            ->join('cleanse_league_teams', 'cleanse_league_event_teams.team_id', '=', 'cleanse_league_teams.id')
            ->select('cleanse_league_event_teams.id', 'cleanse_league_event_teams.team_id', 'cleanse_league_teams.name')
            ->where('cleanse_league_event_teams.teamable_type', 'season')
            ->where('cleanse_league_event_teams.teamable_id', $season_id)
            ->orderBy('cleanse_league_teams.name', 'asc')
            ->get();

        $idVals = [];
        foreach ($seasonTeams as $team) {
            $idVals[] = $team->team_id;
        }

        $teams = Team::whereNotIn('id', $idVals)->orderBy('name', 'asc')->get();

        $this->page['mode'] = 'teams';
        $this->page['season'] = $season_id;
        $this->page['teams'] = $teams;
        $this->page['eteams'] = $seasonTeams;
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
        $search = post('search');

        if (!$search) {
            throw new ValidationException(['name' => 'You must enter at least part of a team\'s name!']);
        }

        $season = Season::with('teams.team')->whereNull('winner_id')->first();

        $seasonTeams = DB::table('cleanse_league_event_teams')
            ->join('cleanse_league_teams', 'cleanse_league_event_teams.team_id', '=', 'cleanse_league_teams.id')
            ->select('cleanse_league_event_teams.id', 'cleanse_league_event_teams.team_id', 'cleanse_league_teams.name')
            ->where('cleanse_league_event_teams.teamable_type', 'season')
            ->where('cleanse_league_event_teams.teamable_id', $season->id)
            ->orderBy('cleanse_league_teams.name', 'asc')
            ->get();

        $idVals = [];
        foreach ($seasonTeams as $team) {
            $idVals[] = $team->team_id;
        }

        $list = Team::where('name', 'like', '%' . $search . '%')
            ->whereNotIn('id', $idVals)
            ->orderBy('name', 'asc')
            ->get();

        $this->page['items'] = $list;
        $this->page['season'] = post('season');
    }

    protected function modeSchedule()
    {
        $check = Season::whereNull('finished_at')
            ->has('matches')
            ->first();

        if ($check) {
            Flash::error('Season: "' . $check->name . '" has a schedule already.');

            Session::flash('flashSuccess', true);

            return Redirect::refresh();
        }

        $season = Season::whereNull('finished_at')->get();

        if (!$season->count() > 0) {
            Flash::error('There is currently no season.');

            Session::flash('flashSuccess', true);

            return Redirect::refresh();
        }

        $this->page['season'] = $season;
    }

    public function onCreateSchedule()
    {
        $this->post = Input::all();

        $schedule = new Scheduler;

        $seasonSchedule = $schedule->createSchedule($this->post);

        Event::fire('cleanse.league',
            [Auth::getUser(), 'season.schedule.create', $seasonSchedule]);

        Flash::success('Schedule was created.');
        Session::flash('flashSuccess', true);

        return Redirect::to('/manager/match');
    }

    protected function getSeasonsList()
    {
        return Season::orderBy('id', 'desc')
            ->paginate(20);
    }
}
