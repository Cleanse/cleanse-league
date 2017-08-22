<?php namespace Cleanse\League\Models;

use Markdown;
use Model;
use Str;

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
    public $table = 'cleanse_league_matches';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['team_one', 'team_two', 'takes_place_at'];

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
        'games' => 'Cleanse\League\Models\Game'
    ];
}
