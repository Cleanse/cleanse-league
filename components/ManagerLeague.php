<?php namespace Cleanse\League\Components;

use Flash;
use Input;
use Redirect;
use Request;
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
            'description' => 'Manage the leagues settings.'
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
     * @return array
     */
    public function getLeagueFormAttributes()
    {
        $attributes = [];

        $attributes['method'] = 'POST';
        $attributes['data-request'] = $this->alias . '::onFormSend';
        $attributes['data-request-update'] = "'" . $this->alias . "::flash-message':'#cleanse-league-form-message','"
            . $this->alias . "::success':'#cleanse-league-form'";
        $attributes['data-request-confirm'] = 'Is the information correct?';

        return $attributes;
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

        if (!Request::ajax()) {
            Flash::success('Schedule was created.');
            Session::flash('flashSuccess', true);

            return Redirect::refresh();
        } else {
            $this->post = [];
            $this->postData = [];
            $this->page['flashSuccess'] = true;
            $this->page['type'] = 'success';
            $this->page['message'] = 'League was updated.';
        }
    }
}
