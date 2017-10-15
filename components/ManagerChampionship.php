<?php namespace Cleanse\League\Components;

use Auth;
use Event;
use Flash;
use Redirect;
use Session;
use Cms\Classes\ComponentBase;
use Cleanse\League\Models\League;
use Cleanse\League\Models\Championship;

class ManagerChampionship extends ComponentBase
{
    public $championship;
    public $championships;

    public function componentDetails()
    {
        return [
            'name'        => 'Manage Championships',
            'description' => 'Control panel for the championship\'s section.'
        ];
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->addJs('assets/js/bootstrap-4-min.js');

        $this->championship = $this->page['championship'] = $this->getChampionship();
        $this->championships = $this->page['championships'] = $this->getChampionships();
    }

    public function onUpdate()
    {
        $mode = post('mode');

        if (!$mode) {
            $mode = 'list';
        }

        $this->page['mode'] = $mode;

        if ($mode == 'list') {
            $this->page['championships'] = $this->getChampionship();
        }

        if ($mode == 'create') {
            return $this->modeCreate();
        }

        if ($mode == 'rules') {
            return $this->modeRules();
        }

        if ($mode == 'edit') {
            return $this->modeEdit();
        }
    }

    protected function modeCreate()
    {
        $check = Championship::whereNull('winner_id')->first();

        if ($check) {
            Flash::error('Championship series: "' . $check->name . '" has not completed yet.');

            Session::flash('flashSuccess', true);

            return Redirect::refresh();
        }

        $leagues = League::orderBy('id', 'desc')->get();

        if (!$leagues->count() > 0) {
            return Redirect::to('/manager/league');
        }

        $this->page['leagues'] = $leagues;
    }

    public function onCreateChampionship()
    {
        $lId = post('league_id');
        $championshipName = post('name');
        $championshipSlug = post('slug') ?? null;

        $league = League::find($lId);

        $championship = $league->championships()->create([
            'name' => $championshipName,
            'slug' => $championshipSlug
        ]);

        Event::fire('cleanse.league',
            [Auth::getUser(), 'championship.create', $championship]);

        Flash::success('Championship: "' . $championship->name . '" was created.');

        Session::flash('flashSuccess', true);

        return Redirect::refresh();
    }

    protected function modeRules()
    {
        $check = Championship::whereNull('winner_id')->first();

        if (is_null($check)) {
            Flash::error('There is no championship series, please create one first.');

            Session::flash('flashSuccess', true);

            return Redirect::refresh();
        }

        $this->page['championship'] = $check;
    }

    public function onModifyRules()
    {
        $cId = post('id');
        $championshipRules = post('rules');

        $championship = Championship::find($cId);

        $championship->championship_rules = $championshipRules;

        $championship->save();

        Event::fire('cleanse.league',
            [Auth::getUser(), 'championship.rules.update', $championshipRules]);

        Flash::success('Championship rules updated.');

        Session::flash('flashSuccess', true);

        return Redirect::refresh();
    }

    protected function modeEdit()
    {
        $cId = post('championship_id');

        $this->page['championship'] = Championship::find($cId);
    }

    public function onEditChampionship()
    {
        $editChampionship = Championship::find(post('championship_id'));

        $editChampionship->name = post('name');

        $editChampionship->save();

        Event::fire('cleanse.league',
            [Auth::getUser(), 'championship.edit', $editChampionship]);

        Flash::success('Championship ' . $editChampionship->name . ' was edited.');

        Session::flash('flashSuccess', true);

        return Redirect::refresh();
    }

    public function getChampionship()
    {
        return Championship::orderBy('id', 'desc')->first();
    }

    public function getChampionships()
    {
        return Championship::orderBy('created_at', 'desc')->get();
    }
}
