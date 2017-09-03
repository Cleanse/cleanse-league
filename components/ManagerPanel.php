<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;
use Cleanse\League\Models\League;

class ManagerPanel extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'PvPaissa League Admin Panel',
            'description' => 'Control panel for the League Manager.'
        ];
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->page['league'] = $this->getLeague();
    }

    public function getLeague()
    {
        return League::with(['championships' => function ($q) {
            $q->orderBy('id', 'desc');
            $q->first();
        }])->first()->toArray();
    }
}
