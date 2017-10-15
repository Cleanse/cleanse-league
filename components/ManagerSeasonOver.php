<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;

class ManagerSeasonOver extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'League Manager: Season Over',
            'description' => 'End the league\'s season.'
        ];
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->addJs('assets/js/bootstrap-4-min.js');
    }

    /**
     * 1. Delete incomplete games if season not over? Force ties?
     * 2. Get teams and sort by current standings.
     * 3. Build initial bracket? Just show bracket and let admin build.
     */

    public function seasonOver()
    {
        //
    }

    private function incompleteMatches()
    {
        //
    }

    private function getTeams()
    {
        //
    }

    private function getBracket()
    {
        //
    }
}
