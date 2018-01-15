<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;
use Cleanse\League\Models\Season;
use Cleanse\League\Models\Team;

class LeagueScheduleTeam extends ComponentBase
{
    private $season;
    private $matches;

    public function componentDetails()
    {
        return [
            'name' => 'Team League Schedule',
            'description' => 'Team league schedule component.'
        ];
    }

    public function defineProperties()
    {
        return [
            'season' => [
                'title' => 'League Season',
                'description' => 'The season of the league.',
                'default' => '{{ :season }}',
                'type' => 'string'
            ],
            'team' => [
                'title' => 'League Team',
                'description' => 'Look up the team by slug.',
                'default' => '{{ :team }}',
                'type' => 'string'
            ]
        ];
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->addJs('assets/js/bootstrap-4-min.js');

        if (!$this->getTeamSchedule()) {
            return redirect('/league');
        }

        return;
    }

    /**
     * @return bool
     */
    private function getTeamSchedule()
    {
        $team = $this->getEventTeam();

        if (!$team->event_teams->count() > 0) {
            return false;
        }

        $this->season = $this->getTeamMatches($team->event_teams[0]);

        $this->page['team'] = $team;
        $this->page['matches'] = $this->sortTeamMatches();

        return true;
    }

    /**
     * @return mixed
     */
    private function getEventTeam()
    {
        $teamSlug = $this->property('team');

        return $eventTeam = Team::where('slug', '=', $teamSlug)
            ->with(['event_teams' => function ($q) {
                if ($season = $this->property('season')) {
                    $q->where('teamable_id', '=', $season);
                } else {
                    $q->orderBy('id', 'desc');
                }
                $q->where('teamable_type', '=', 'season');
            }])
            ->first();
    }

    /**
     * @param $team
     * @return mixed
     */
    private function getTeamMatches($team)
    {
        $matches = Season::where('id', '=', $team->teamable_id)
            ->whereHas('matches', function ($query) use ($team) {
                $query->where('team_one', '=', $team->id);
                $query->orWhere('team_two', '=', $team->id);
            })
            ->with(['matches' => function ($query) use ($team) {
                $query->where('team_one', '=', $team->id);
                $query->orWhere('team_two', '=', $team->id);
                $query->orderBy('takes_place_during', 'asc');
                $query->with(['one.team', 'two.team']);
            }])->first();

        return $matches;
    }

    /**
     * @return mixed $collection
     */
    public function sortTeamMatches()
    {
        $matches = $this->season->matches->groupBy('takes_place_during');

        if ($week = $this->property('week')) {
            $matches = $matches->slice($week - 1, 1);
        }

        return $matches;
    }
}
