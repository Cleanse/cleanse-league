<?php namespace Cleanse\League\Components;

use DB;
use Exception;
use Flash;
use Input;
use Redirect;
use Session;
use Cms\Classes\ComponentBase;
use Cleanse\League\Models\Season;
use Cleanse\League\Models\Championship;
use Cleanse\League\Models\Team;
use Cleanse\League\Models\EventTeam;
use Cleanse\League\Classes\Scheduler\SeasonScheduler as Scheduler;

class ManagerSeason extends ComponentBase
{
    public $mode;
    public $model;

    public function componentDetails()
    {
        return [
            'name' => 'PvPaissa League Manager Log',
            'description' => 'PvPaissa League Manager log of actions.'
        ];
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
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
        $check = Season::whereNull('finished_at')->first();

        if ($check) {
            Flash::error('Season: "' . $check->name . '" has not completed yet.');

            Session::flash('flashSuccess', true);

            return Redirect::refresh();
        }

        $championships = Championship::orderBy('id', 'desc')->get();

        if (!$championships->count() > 0) {
            return Redirect::to('/manager/championship');
        }

        $this->page['championships'] = $championships;
    }

    protected function modeEdit()
    {
        $this->model = post('season_id');

        $this->page['season'] = Season::find($this->model);
    }

    protected function modeTeams()
    {
        $check = Season::whereNull('finished_at')
            ->has('matches')
            ->first();

        if ($check) {
            Flash::error('Season: "' . $check->name . '" has a locked roster.');

            Session::flash('flashSuccess', true);

            return Redirect::refresh();
        }

        $season = Season::with('teams.team')->whereNull('finished_at')->first();

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

    protected function modeSchedule()
    {
        $this->page['season'] = Season::whereNull('finished_at')->get();
    }

    public function onCreateSeason()
    {
        $c = post('championship');

        $championship = Championship::find($c);

        $season = $championship->seasons()->create([
            'name' => post('name')
        ]);

        Flash::success('Season: "' . $season->name . '" was created.');

        Session::flash('flashSuccess', true);

        return Redirect::refresh();
    }

    public function onEditSeason()
    {
        $editSeason = Season::find(post('season_id'));

        $editSeason->name = post('name');

        $editSeason->save();

        Flash::success('Season ' . $editSeason->name . ' was edited.');

        Session::flash('flashSuccess', true);

        return Redirect::refresh();
    }

    public function onAddTeam()
    {
        $team_id = post('team_id');
        $season_id = post('season_id');

        //Create if

        $season = Season::with('teams')->find($season_id);
        $season->teams()->create([
            'team_id' => $team_id,
        ]);

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

        EventTeam::find($team_id)->delete();

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
        $attributes['data-request-update'] = "'" . $this->alias . "::team-search':'#team-list'";
        $attributes['class'] = 'form-inline';

        return $attributes;
    }

    public function onSearchForTeam()
    {
        $searchData = post('search');
        $season_id = post('season');

        if (!$searchData) {
            throw new ValidationException(['name' => 'You must enter at least part of a team\'s name!']);
        }

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

        $list = Team::where('name', 'like', '%' . $searchData . '%')
            ->whereNotIn('id', $idVals)
            ->orderBy('name', 'asc')->get();

        $this->page['items'] = $list;
        $this->page['season'] = $season_id;
        $this->page['search'] = $searchData;
    }

    /**
     * @return array
     */
    public function getScheduleFormAttributes()
    {
        $attributes = [];

        $attributes['method'] = 'POST';
        $attributes['data-request'] = $this->alias . '::onFormSendSchedule';
        $attributes['data-request-update'] = "'" . $this->alias . "::cleanse-message':'#cleanse-league-form-message','"
            . $this->alias . "::schedule':'#editor-area'";
        $attributes['data-request-confirm'] = 'Is the information correct?';

        return $attributes;
    }

    /**
     * Schedule maker method
     */
    public function onFormSendSchedule()
    {
        $this->post = Input::all();

        $schedule = new Scheduler;

        try {
            $this->page['result'] = $schedule->createSchedule($this->post);

            Flash::success('Schedule was created.');
            Session::flash('flashSuccess', true);

            $this->post = [];
        } catch (Exception $e) {
            $this->page['result'] = 'Caught exception: ' . $e->getMessage();

            Flash::error('Something went wrong creating schedule: ' . $e->getMessage());
        }
    }

    protected function getSeasonsList()
    {
        return Season::orderBy('id', 'desc')
            ->paginate(20);
    }
}
