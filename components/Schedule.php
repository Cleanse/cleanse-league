<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;

class Schedule extends ComponentBase
{
    private $league;
    private $season;
    private $matches;
    private $week;
    private $teams;

    public function componentDetails()
    {
        return [
            'name'        => 'League Schedule',
            'description' => 'League schedule component.'
        ];
    }

    ///league/aether-cup/schedule/1/1
    ///league/aether-cup/schedule/1
    public function defineProperties()
    {
        return [
            'league' => [
                'title'       => 'League Slug',
                'description' => 'Look up the league by slug.',
                'default'     => '{{ :league }}',
                'type'        => 'string'
            ],
            'season' => [
                'title'       => 'League Season',
                'description' => 'The season of the league.',
                'default'     => '{{ :season }}',
                'type'        => 'string'
            ],
            'week' => [
                'title'       => 'League Week',
                'description' => 'Look up the week by slug.',
                'default'     => '{{ :week }}',
                'type'        => 'string'
            ],
        ];
    }

    public function onRun()
    {
        $this->league = $this->page['league'] = $this->property('league');
        $this->season = $this->page['season'] = $this->property('season');
        $this->week = $this->page['week'] = $this->getWeek();
        $this->matches = $this->page['matches'] = $this->getMatches();
    }

    private function getWeek()
    {
        if ($this->property('week')) {
            $week = $this->property('week');
        } else {
            $week = 1;
        }

        return Schedule::where('week', $week)
            ->orderBy('rank')
            ->paginate(50);
    }

    private function getMatches()
    {
        return Match::where('week', $this->week->id)
            ->get();
    }
}