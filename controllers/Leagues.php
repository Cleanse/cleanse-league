<?php namespace Cleanse\League\Controllers;

use Flash;
use Redirect;
use BackendMenu;
use Backend\Classes\Controller;
use ApplicationException;
use Cleanse\League\Models\League;

class Leagues extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = ['cleanse.league.access_leagues'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Cleanse.League', 'league', 'leagues');
    }

    public function index()
    {
        $this->vars['leaguesTotal'] = League::count();

        $this->asExtension('ListController')->index();
    }

    public function create()
    {
        BackendMenu::setContextSideMenu('new_league');

        $this->bodyClass = 'compact-container';

        return $this->asExtension('FormController')->create();
    }

    public function update($recordId = null)
    {
        $this->bodyClass = 'compact-container';

        return $this->asExtension('FormController')->update($recordId);
    }
}
