<?php namespace Cleanse\League;

use Backend;
use October\Rain\Database\Relations\Relation;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name'        => 'PvPaissa League',
            'description' => 'A plugin that adds league functionality.',
            'author'      => 'Paul Lovato',
            'icon'        => 'icon-newspaper-o'
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
    }

    public function registerComponents()
    {
        return [
            //frontend "done"
            'Cleanse\League\Components\LeagueAbout' => 'cleanseLeagueAbout',
            'Cleanse\League\Components\LeagueHome' => 'cleanseLeagueHome',
            'Cleanse\League\Components\LeagueRules' => 'cleanseLeagueRules',
            'Cleanse\League\Components\LeagueTeam' => 'cleanseLeagueTeam',
            'Cleanse\League\Components\LeagueVision' => 'cleanseLeagueVision',

            'Cleanse\League\Components\SeasonSchedule' => 'cleanseLeagueSeasonSchedule',

            //manager
            'Cleanse\League\Components\ManagerPanel' => 'cleanseLeagueManager',
            'Cleanse\League\Components\ManagerLeague' => 'cleanseLeagueManagerLeague',
            'Cleanse\League\Components\ManagerChampionships' => 'cleanseLeagueManagerChampionships',
            'Cleanse\League\Components\ManagerSeasons' => 'cleanseLeagueManagerSeasons',

            //test
            'Cleanse\League\Components\TestBracket' => 'leagueBracket',

            //moving to new plugin
            'Cleanse\League\Components\Highlight' => 'cleanseLeagueHighlight',

            //tbd
            'Cleanse\League\Components\SeasonStandings' => 'cleanseLeagueSeasonStandings',
            'Cleanse\League\Components\SeasonStats' => 'cleanseLeagueSeasonStats',

            'Cleanse\League\Components\SeasonTeam' => 'cleanseLeagueSeasonTeam',
            'Cleanse\League\Components\SeasonPlayer' => 'cleanseLeagueSeasonPlayer',

            'Cleanse\League\Components\TournamentGroups' => 'cleanseLeagueTourneyGroups',
            'Cleanse\League\Components\TournamentBracket' => 'cleanseLeagueTourneyBracket',

            'Cleanse\League\Components\ManagerLog' => 'cleanseLeagueManagerLog',

            'Cleanse\League\Components\History' => 'cleanseLeagueHistory'
        ];
    }

    public function registerPermissions()
    {
        return [
            'cleanse.league.access_league' => [
                'tab'   => 'League',
                'label' => 'Manage Leagues'
            ]
        ];
    }

    public function registerNavigation()
    {
        return [
            'league' => [
                'label'       => 'League',
                'url'         => Backend::url('cleanse/league/leagues'),
                'icon'        => 'facetime-video',
                'iconSvg'     => 'plugins/cleanse/league/assets/images/league.svg',
                'permissions' => ['cleanse.league.*'],
                'order'       => 30,

                'sideMenu' => [
                    'new_streamer' => [
                        'label'       => 'New League',
                        'icon'        => 'icon-plus',
                        'url'         => Backend::url('cleanse/league/leagues/create'),
                        'permissions' => ['cleanse.league.access_leagues']
                    ],
                    'streamersmini' => [
                        'label'       => 'Leagues',
                        'icon'        => 'icon-copy',
                        'url'         => Backend::url('cleanse/league/leagues'),
                        'permissions' => ['cleanse.league.access_leagues']
                    ]
                ]
            ]
        ];
    }
}
