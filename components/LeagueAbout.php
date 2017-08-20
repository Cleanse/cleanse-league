<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;
use Cleanse\League\Models\League;

class LeagueAbout extends ComponentBase
{
    public $about;

    public function componentDetails()
    {
        return [
            'name'        => 'About',
            'description' => 'League About.'
        ];
    }

    public function onRun()
    {
        $this->addCss('/plugins/cleanse/league/assets/css/league.css');
        $this->about = $this->page['about'] = $this->getAbout();
    }

    public function getAbout()
    {
        $about = League::find(1);

        if (!$about) {
            return [
                'name' => 'Aether League',
                'about_html' => '<p>We want this to be as successful as possible so Square Enix can take note and 
                                 maybe in the distant future give PVP more support.</p>'
            ];
        }

        return $about;
    }
}
