<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;
use Cleanse\League\Models\Championship;

class ManagerChampionships extends ComponentBase
{
    public $championship;
    public $championships;

    public function componentDetails()
    {
        return [
            'name'        => 'Manage Championships',
            'description' => 'Control panel for the Championships section.'
        ];
    }

    public function onRun()
    {
        $this->championship = $this->page['championship'] = $this->getChampionship();
        $this->championships = $this->page['championships'] = $this->getChampionships();
    }

    public function getChampionship()
    {
        return Championship::orderBy('id', 'desc')->first();
    }

    public function getChampionships()
    {
        return Championship::orderBy('id', 'desc')->get();
    }
}
