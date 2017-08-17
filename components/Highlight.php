<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;

class Highlight extends ComponentBase
{
//    public $about;

    public function componentDetails()
    {
        return [
            'name'        => 'Highlight',
            'description' => 'Highlight bar component.'
        ];
    }

//    public function onRun()
//    {
//        $this->addCss('/plugins/cleanse/league/assets/css/league.css');
//        $this->about = $this->page['about'] = $this->getAbout();
//    }
//
//    public function getAbout()
//    {
//        return 'Here is the about.';
//    }
}
