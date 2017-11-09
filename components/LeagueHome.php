<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;
use Cleanse\League\Models\League;
use Cleanse\League\Models\Championship;
use Cleanse\League\Models\Season;
use RainLab\Blog\Models\Post as BlogPost;
use RainLab\Blog\Models\Category as BlogCategory;

class LeagueHome extends ComponentBase
{
    public $league;
    public $championship;
    public $seasonal;
    public $season;

    public function componentDetails()
    {
        return [
            'name' => 'League Home',
            'description' => 'League index page.'
        ];
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->addJs('assets/js/bootstrap-4-min.js');

        $this->league = $this->page['league'] = $this->getLeague();

        $this->getCurrentHappening();
    }

    public function getLeague()
    {
        return League::first();
    }

    public function getCurrentHappening()
    {
        $this->page['articles'] = $this->getArticles();

        $championshipFinals = $this->checkChampionship();
        if (isset($championshipFinals)) {
            $this->championship = $this->page['championship'] = $championshipFinals;
            return;
        }

        $seasonFinals = $this->checkSeasonal();
        if (isset($seasonFinals)) {
            $this->seasonal = $this->page['seasonal'] = $seasonFinals;
            return;
        }

        $seasonMatches = $this->checkSeason();
        if (isset($seasonMatches)) {
            $this->page['season'] = $this->season;
            $this->page['matches'] = $seasonMatches;
            return;
        }

        return $this->initialState();
    }

    private function initialState()
    {
        return;
    }

    protected function checkChampionship()
    {
        return Championship::whereHas('tourneys', function ($query) {
            $query->whereNull('winner_id');
        })
            ->with([
                'tourneys' => function ($query) {
                    $query->whereNull('winner_id');
                }
            ])->first();
    }

    protected function checkSeasonal()
    {
        return Season::whereHas('tourneys', function ($query) {
            $query->whereNull('winner_id');
        })
            ->with([
                'tourneys' => function ($query) {
                    $query->where('winner_id');
                }
            ])->first();
    }

    protected function checkSeason()
    {
        $season = Season::whereHas('matches', function ($query) {
            $query->whereNull('winner_id');
        })
            ->with([
                'matches' => function ($query) {
                    $query->whereNull('winner_id');
                    $query->orderBy('takes_place_during', 'asc');
                    $query->with(['one.team', 'two.team']);
                }
            ])->first();

        if (!isset($season)) {
            return false;
        }

        $this->season = $season;

        $matches = $season->matches->groupBy('takes_place_during');

        return $matches->slice(0, 1);
    }

    protected function getArticles()
    {
        $slug = 'aether-league';

        $category = BlogCategory::whereSlug($slug)->first();

        return BlogPost::with('categories')
            ->orderBy('id', 'desc')
            ->listFrontEnd([
            'perPage'    => 5,
            'category'   => $category->id
        ]);
    }
}
