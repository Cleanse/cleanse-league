<?php namespace Cleanse\League\Components;

use Cms\Classes\ComponentBase;
use Cleanse\League\Models\Championship;
use Cleanse\League\Models\EventPlayer;

class LeagueChampionshipStats extends ComponentBase
{
    public $stats;

    public function componentDetails()
    {
        return [
            'name' => 'League Manager: Championship Stats',
            'description' => 'Championship Statistics Page.',
        ];
    }

    public function onRun()
    {
        $this->addCss('assets/css/league.css');
        $this->addJs('assets/js/bootstrap-4-min.js');

        $this->initStatsData();
    }

    public function initStatsData()
    {
        $stats = $this->getStats();
        $this->page['damage'] = $stats->sortByDesc('damage')->take(3);
        $this->page['healing'] = $stats->sortByDesc('healing')->take(3);
        $this->page['medals'] = $stats->sortByDesc('medals')->take(3);
        $this->page['least'] = $stats->sortBy('medals')->take(3);
        $this->page['kills'] = $stats->sortByDesc('kills')->take(3);
        $this->page['deaths'] = $stats->sortByDesc('deaths')->take(3);
        $this->page['alive'] = $stats->sortBy('deaths')->take(3);
        $this->page['assists'] = $stats->sortByDesc('assists')->take(3);
    }

    private function getStats()
    {
        $seasons = $this->getSeasonIds();

        $seasonalPlayers = $this->getSeasonalPlayers($seasons);

        $mergedPlayers = $this->mergePlayers($seasonalPlayers);

        return $mergedPlayers;
    }

    private function getSeasonIds()
    {
        $this->page['championship'] = $season = Championship::orderBy('id', 'desc')
            ->with([
                'seasons' => function ($q) {
                    $q->orderBy('id', 'desc');
                }
            ])
            ->first();

        $seasons = [];

        foreach ($season->seasons as $s) {
            $seasons[] = $s->id;
        }

        return $seasons;
    }

    private function getSeasonalPlayers($seasons)
    {
        $players = EventPlayer::where('game_total', '>=', 1)
            ->where('playerable_type', '=', 'season')
            ->whereIn('playerable_id', $seasons)
            ->with('player')
            ->get();

        return $players->groupBy('player_id');
    }

    private function mergePlayers($playerGroup)
    {
        $merged = [];

        foreach ($playerGroup as $players) {
            if ($players->count() == 1) {
                $merged[] = $players;

                continue;
            }

            $merged[] = $this->mergePlayer($players);
        }

        return $merged;
    }

    private function mergePlayer($players)
    {
        $merged = collect([
            'player_id' => $players[0]->player_id,
            'jobs' => $this->jobsSum($players),
            'kills' => $players->sum('kills'),
            'deaths' => $players->sum('deaths'),
            'assists' => $players->sum('assists'),
            'damage' => $players->sum('damage'),
            'healing' => $players->sum('healing'),
            'medals' => $players->sum('medals'),
            'match_total' => $players->sum('match_total'),
            'match_wins' => $players->sum('match_wins'),
            'match_losses' => $players->sum('match_losses'),
            'game_total' => $players->sum('game_total'),
            'game_wins' => $players->sum('game_wins'),
            'game_losses' => $players->sum('game_losses'),
            'game_ties' => $players->sum('game_ties')
        ]);

        dd($merged);

        return $merged;
    }

    private function jobsSum($players)
    {
        $jobsStats = [];

        foreach ($players as $player) {

            if (is_null($player->jobs)) {
                return json_encode([]);
            }

            $oldJobs = json_decode($player->jobs, true);

            dd($player->toArray());

            if ($player->player_job == '0') {
                continue;
            }

            if ($player->player_job == '') {
                continue;
            }

            if (!isset($jobsStats[$player->player_job])) {
                $jobsStats[$player->player_job] = 1;
                continue;
            }

            $jobsStats[$player->player_job]++;
        }

        return json_encode($jobsStats);
    }
}
