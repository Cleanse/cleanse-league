<?php namespace Cleanse\League\Components;

use Auth;
use Event;
use Flash;
use Redirect;
use Session;
use Cms\Classes\ComponentBase;
use Cleanse\League\Models\League;

class ManagerLeague extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Manage League Settings',
            'description' => 'Manage the league\'s settings.'
        ];
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->addJs('assets/js/bootstrap-4-min.js');

        $this->page['flashSuccess'] = Session::get('flashSuccess');
        $this->page['league'] = League::find(1);
    }

    public function onEditLeague()
    {
        $slug = post('slug');
        $about = post('about');

        $editLeague = League::find(1);

        $editLeague->name = post('name');

        if (!is_null($slug)) {
            $editLeague->slug = post('slug');
        }

        if (!is_null($about)) {
            $editLeague->about = post('about');
        }

        $editLeague->save();

        Event::fire('cleanse.league',
            [Auth::getUser(), 'league.edit', $editLeague]);

        Flash::success('League ' . $editLeague->name . ' was edited.');

        Session::flash('flashSuccess', true);

        return Redirect::refresh();
    }
}
