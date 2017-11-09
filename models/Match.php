<?php namespace Cleanse\League\Models;

use Model;

/**
 * @property integer $id
 * @property integer $team_one
 * @property integer $team_two
 * @property integer $team_one_score
 * @property integer $team_two_score
 * @property integer $winner_id
 * @property string $matchable_id
 * @property string $matchable_type
 * @property string takes_place_at
 */
class Match extends Model
{
    use \Cleanse\Urls\Classes\Shareable;

    public $table = 'cleanse_league_matches';
    protected $primaryKey = 'id';
    public $incrementing = false;

    /**
     * @var array Generate shareable string for primary key.
     */
    protected $shareable = ['id' => ['default']];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['team_one', 'team_two', 'takes_place_during'];

    /**
     * Relationships
     */
    public $morphTo = [
        'matchable' => []
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
        'games' => 'Cleanse\League\Models\MatchGame'
    ];

    //country -> users -> posts
    //match   -> games -> players
    public $hasManyThrough = [
        'players' => [
            'Cleanse\League\Models\MatchGamePlayer',
            'key' => 'match_id',
            'through' => 'Cleanse\League\Models\MatchGame',
            'throughKey' => 'game_id'
        ],
    ];
}
