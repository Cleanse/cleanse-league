<?php namespace Cleanse\League\Components;

use Cleanse\League\Models\Championship;
use Cms\Classes\ComponentBase;

class LeagueRules extends ComponentBase
{
    public $rules;

    public function componentDetails()
    {
        return [
            'name'        => 'Rules',
            'description' => 'League Rules.'
        ];
    }

    public function onRun()
    {
        $this->addCss('/plugins/cleanse/league/assets/css/league.css');
//        $this->rules = $this->page['rules'] = $this->getRules();
    }

//    public function getRules()
//    {
//        return html_entity_decode(Championship::find(1)->rules_html);
//    }
}
