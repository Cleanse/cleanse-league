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
        $this->addCss('/plugins/cleanse/league/assets/css/league.css');

        $this->getMatch();

        if ($spoiler = $this->property('spoiler')) {
            $this->page['spoiler'] = true;
        }
    }

    public function getMatch()
    {
        if ($matchId = $this->property('match')) {
            $match = Match::whereId($matchId)->with(['one' => function ($q) {
                $q->with('team');
            }, 'two' => function ($q) {
                $q->with('team');
            }])->first();
        }

        if (isset($match)) {
            $this->match = $this->page['match'] = $match;
        }
    }
}
