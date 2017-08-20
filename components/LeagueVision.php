<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;
use Cleanse\League\Models\League;

class LeagueVision extends ComponentBase
{
    public $vision;

    public function componentDetails()
    {
        return [
            'name'        => 'Vision',
            'description' => 'League Vision.'
        ];
    }

    public function onRun()
    {
        $this->addCss('/plugins/cleanse/league/assets/css/league.css');
        $this->vision = $this->page['vision'] = $this->getVision();
    }

    public function getVision()
    {
        //$vision = League::find(1)->vision_html;

        $vision = false;
        if (!$vision) {
            return '<p>To improve, support, and promote the PVP community. To grow the community to a healthy level. 
            To break down any barriers of nervousness or fear and to promote a more welcoming environment.</p>';
        }

        return $vision;
    }
}
