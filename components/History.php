<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;
use Cleanse\League\Models\League;

class History extends ComponentBase
{
    public $league;

    public function componentDetails()
    {
        return [
            'name'        => 'History',
            'description' => 'League History page.'
        ];
    }

    public function onRun()
    {
        $this->addCss('/plugins/cleanse/league/assets/css/league.css');
        $this->league = $this->page['league'] = $this->getLeague();
    }

    public function getLeague()
    {
        $league = League::find(1);

        if (!$league) {
            //
        }

        return $league;
    }
}
