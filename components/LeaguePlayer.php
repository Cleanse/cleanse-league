<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;
use Cleanse\League\Models\Player as PlayerModel;

class LeaguePlayer extends ComponentBase
{
    public $player;

    public function componentDetails()
    {
        return [
            'name'        => 'League Player Page',
            'description' => 'Individual player page.',
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'Player Slug',
                'description' => 'Player slug.',
                'default'     => '{{ :slug }}',
                'type'        => 'string',
            ]
        ];
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->addJs('assets/js/bootstrap-4-min.js');

        $this->player = $this->page['player'] = $this->getPlayer();
    }

    public function getPlayer()
    {
        $playerId = $this->property('slug');

        $player = PlayerModel::whereSlug($playerId)
            ->first();

        if (!$player || !$player->exists) {
            $this->setStatusCode(404);
            return $this->controller->run('404');
        }

        return $player;
    }
}
