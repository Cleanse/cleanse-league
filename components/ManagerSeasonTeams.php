<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;

class ManagerSeasonTeams extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Manage Season Teams',
            'description' => 'Control panel for the seasonal team point\'s section.'
        ];
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->addJs('assets/js/bootstrap-4-min.js');

        //manually edit points
    }
}
