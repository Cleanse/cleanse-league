<?php namespace Cleanse\League\Models;

use Model;

/**
 * @property integer $id
 * @property integer $match_id
 * @property integer $team_one
 * @property integer $team_two
 * @property integer $winner_id
 * @property string  $vod
 * @property integer $duration
 * @property integer $value
 */
class MatchGame extends Model
{
    public $table = 'cleanse_league_match_games';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'match_id',
        'team_one',
        'team_two',
        'winner_id',
        'vod',
        'duration'
    ];

    /***
     * Screenshot
     * @var array
     */
    public $attachOne = [
        'screenshot' => ['System\Models\File']
    ];

    public $hasOne = [
        'one' => [
            'Cleanse\League\Models\EventTeam',
            'key' => 'id',
            'otherKey' => 'team_one'
        ],
        'two' => [
            'Cleanse\League\Models\EventTeam',
            'key' => 'id',
            'otherKey' => 'team_two'
        ],
        'winner' => [
            'Cleanse\League\Models\EventTeam',
            'key' => 'id',
            'otherKey' => 'winner_id'
        ]
    ];

    public $hasMany = [
        'players' => [
            'Cleanse\League\Models\MatchGamePlayer',
            'key' => 'game_id',
            'otherKey' => 'id'
        ]
    ];
}
