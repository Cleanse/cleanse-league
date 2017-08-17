<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;
use Cleanse\League\Models\Tournament;

class TournamentBracket extends ComponentBase
{
    public $tournament;

    public function componentDetails()
    {
        return [
            'name'        => 'Tournament Bracket',
            'description' => 'Tournament bracket page component.'
        ];
    }

    public function defineProperties()
    {
        return [
            'tourney' => [
                'title'       => 'Tourney ID',
                'description' => 'Look up the tourney bracket using the supplied id value.',
                'default'     => '{{ :tourney }}',
                'type'        => 'string'
            ],
            'slug' => [
                'title'       => 'Tourney Slug',
                'description' => 'Pretty URL Slug.',
                'default'     => '{{ :slug }}',
                'type'        => 'string'
            ],
        ];
    }

    public function onRun()
    {
        $this->addCss('/plugins/cleanse/league/assets/css/league.css');
        $this->tournament = $this->page['tournament'] = $this->loadTournament();
    }

    protected function loadTournament()
    {
        $tourney = $this->property('tourney');
        $tournament = Tournament::where('id', $tourney)->first();

        return $tournament;
    }
}