<?php namespace Cleanse\League\Components;

use Session;
use Cms\Classes\ComponentBase;
use Cleanse\League\Models\Match;

class ManagerMatchList extends ComponentBase
{
    public $match;

    public function componentDetails()
    {
        return [
            'name' => 'League Manager: List of Matches',
            'description' => 'View a list of the league\'s matches.'
        ];
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->addJs('assets/js/bootstrap-4-min.js');

        $this->page['flashSuccess'] = Session::get('flashSuccess');
        $this->page['matches'] = $this->getIncompleteMatchesList();
        $this->page['completed'] = $this->getCompleteMatchesList();
    }

    public function onUpdate()
    {
        $mode = post('mode');

        if (!$mode) {
            $mode = 'list';
        }

        $this->page['mode'] = $mode;

        if ($mode == 'list') {
            $this->page['matches'] = $this->getIncompleteMatchesList();
            $this->page['completed'] = $this->getCompleteMatchesList();
        }
    }

    public function getIncompleteMatchesList()
    {
        return Match::with(['one.team', 'two.team'])
            ->whereNull('winner_id')
            ->orderBy('takes_place_at', 'asc')
            ->paginate(20);
    }

    public function getCompleteMatchesList()
    {
        return Match::with(['one.team', 'two.team'])
            ->whereNotNull('winner_id')
            ->orderBy('takes_place_at', 'asc')
            ->paginate(20);
    }

    /**
     * @return array
     */
    public function getSearchFormAttributes()
    {
        $attributes = [];

        $attributes['method'] = 'POST';
        $attributes['data-request'] = $this->alias . '::onSearchForMatch';
        $attributes['data-request-update'] = "'" . $this->alias . "::search-results':'#match-list'";
        $attributes['class'] = 'justify-content-end';

        return $attributes;
    }

    public function onSearchForMatch()
    {
        $matchId = post('search');

        $list = Match::with(['one.team', 'two.team'])
            ->where('id', '=', $matchId)
            ->get();

        $this->page['items'] = $list;
    }
}
