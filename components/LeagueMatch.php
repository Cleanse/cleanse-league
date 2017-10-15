<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;
use Cleanse\League\Models\Match;

class LeagueMatch extends ComponentBase
{
    public $match;

    public function componentDetails()
    {
        return [
            'name' => 'League Match',
            'description' => 'League match display component.'
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
            ],
            'spoiler' => [
                'title' => 'Spoilers On',
                'description' => 'Hide the scores if set.',
                'default' => '{{ :spoiler }}',
                'type' => 'string'
            ]
        ];
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->addJs('assets/js/bootstrap-4-min.js');

        $this->initLeagueMatch();
    }

    public function initLeagueMatch()
    {
        $this->match = $this->page['match'] = $this->getMatch();
    }

    public function getMatch()
    {
        $matchId = $this->property('match');

        return Match::whereId($matchId)->with([
            'one.team',
            'two.team',
            'games' => function ($q) {
                $q->with(['one', 'two', 'players.player.player']);
            }])
            ->first();
    }
}
