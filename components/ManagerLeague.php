<?php namespace Cleanse\League\Components;

use Flash;
use Input;
use Redirect;
use Session;
use Cms\Classes\ComponentBase;
use Cleanse\League\Models\League;

class ManagerLeague extends ComponentBase
{
    private $post = [];
    private $postData = [];

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
        $this->page['flashSuccess'] = Session::get('flashSuccess');
        $this->page['league'] = League::find(1);
    }

    /**
     * Escape HTML entities in values.
     */
    private function setPostData()
    {
        foreach (Input::all() as $key => $value) {
            $this->postData[$key] = [
                'value' => e(Input::get($key))
            ];
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function onFormSend()
    {
        $this->post = Input::all();
        $this->setPostData($this->post);

        $league = League::find(1);

        $league->storeFormData($this->postData);

        Flash::success('Your league information was updated.');

        Session::flash('flashSuccess', true);

        return Redirect::refresh();
    }
}
