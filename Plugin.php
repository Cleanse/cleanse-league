<?php namespace Cleanse\League;

use Backend;
use Event;
use October\Rain\Database\Relations\Relation;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name' => 'PvPaissa League',
            'description' => 'A plugin that adds league functionality.',
            'author' => 'Paul Lovato',
            'icon' => 'icon-newspaper-o'
        ];
    }

    public function boot()
    {
        Relation::morphMap([
            'championship' => 'Cleanse\League\Models\Championship',
            'season' => 'Cleanse\League\Models\Season',
            'event' => 'Cleanse\League\Models\Event',
            'tourney' => 'Cleanse\League\Models\Tournament',
        ]);

        Event::listen('cleanse.league',
            'Cleanse\League\Classes\ManagerLog\LeagueHandler');
    }

    public function registerComponents()
    {
        return [
            //frontend "done"
            'Cleanse\League\Components\LeagueAbout' => 'cleanseLeagueAbout',
            'Cleanse\League\Components\LeagueChampionshipPoints' => 'cleanseLeagueChampionshipPoints',
            'Cleanse\League\Components\LeagueChampionshipStats' => 'cleanseLeagueChampionshipStats',
            'Cleanse\League\Components\LeagueHome' => 'cleanseLeagueHome',
            'Cleanse\League\Components\LeagueMatch' => 'cleanseLeagueMatch',
            'Cleanse\League\Components\LeaguePlayer' => 'cleanseLeaguePlayer',
            'Cleanse\League\Components\LeagueRules' => 'cleanseLeagueRules',
            'Cleanse\League\Components\LeagueSchedule' => 'cleanseLeagueSchedule',
            'Cleanse\League\Components\LeagueScheduleTeam' => 'cleanseLeagueScheduleTeam',
            'Cleanse\League\Components\LeagueStandings' => 'cleanseLeagueStandings',
            'Cleanse\League\Components\LeagueStats' => 'cleanseLeagueStats',
            'Cleanse\League\Components\LeagueTeam' => 'cleanseLeagueTeam',

            //manager
            'Cleanse\League\Components\ManagerChampionship' => 'cleanseLeagueManagerChampionship',
            'Cleanse\League\Components\ManagerChampionshipPoints' => 'cleanseLeagueManagerChampionshipPoints',
            'Cleanse\League\Components\ManagerGame' => 'cleanseLeagueManagerGame',
            'Cleanse\League\Components\ManagerLeague' => 'cleanseLeagueManagerLeague',
            'Cleanse\League\Components\ManagerMatch' => 'cleanseLeagueManagerMatch',
            'Cleanse\League\Components\ManagerMatchList' => 'cleanseLeagueManagerMatchList',
            'Cleanse\League\Components\ManagerPanel' => 'cleanseLeagueManagerPanel',
            'Cleanse\League\Components\ManagerPlayer' => 'cleanseLeagueManagerPlayer',
            'Cleanse\League\Components\ManagerSeason' => 'cleanseLeagueManagerSeason',
            'Cleanse\League\Components\ManagerSeasonOver' => 'cleanseLeagueManagerSeasonOver',
            'Cleanse\League\Components\ManagerSeasonTeams' => 'cleanseLeagueManagerSeasonTeams',
            'Cleanse\League\Components\ManagerTeam' => 'cleanseLeagueManagerTeam',
            'Cleanse\League\Components\ManagerTeamEdit' => 'cleanseLeagueManagerTeamEdit',

            //test
            'Cleanse\League\Components\TestBracket' => 'leagueBracket',
        ];
    }

    public function registerPermissions()
    {
        return [
            'cleanse.league.access_league' => [
                'tab' => 'League',
                'label' => 'Manage Leagues'
            ]
        ];
    }

    public function registerNavigation()
    {
        return [
            'league' => [
                'label' => 'League',
                'url' => Backend::url('cleanse/league/leagues'),
                'icon' => 'facetime-video',
                'iconSvg' => 'plugins/cleanse/league/assets/images/league.svg',
                'permissions' => ['cleanse.league.*'],
                'order' => 30,

                'sideMenu' => [
                    'new_streamer' => [
                        'label' => 'New League',
                        'icon' => 'icon-plus',
                        'url' => Backend::url('cleanse/league/leagues/create'),
                        'permissions' => ['cleanse.league.access_leagues']
                    ],
                    'streamersmini' => [
                        'label' => 'Leagues',
                        'icon' => 'icon-copy',
                        'url' => Backend::url('cleanse/league/leagues'),
                        'permissions' => ['cleanse.league.access_leagues']
                    ]
                ]
            ]
        ];
    }
}
