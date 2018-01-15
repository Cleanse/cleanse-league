<?php namespace Cleanse\League\Models;

use Model;

/**
 * Class MatchGamePlayer
 * @package Cleanse\League\Models
 * @property integer $game_id
 * @property integer $team_id
 * @property integer $game_winner_id
 * @property integer $player_id
 * @property string $player_job
 * @property integer $medals
 * @property integer $kills
 * @property integer $deaths
 * @property integer $assists
 * @property integer $damage
 * @property integer $healing
 * @property integer $duration
 */
class MatchGamePlayer extends Model
{
    public $table = 'cleanse_league_match_game_players';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'game_id',
        'player_id',
        'team_id',
        'player_job',
        'medals',
        'kills',
        'deaths',
        'assists',
        'damage',
        'healing',
        'duration'
    ];

    public $hasOne = [
        'player' => [
            'Cleanse\League\Models\EventPlayer',
            'key' => 'id',
            'otherKey' => 'player_id'
        ]
    ];
}
