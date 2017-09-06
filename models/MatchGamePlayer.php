<?php namespace Cleanse\League\Models;

use Model;

/**
 * Class MatchGamePlayer
 * @package Cleanse\League\Models
 * @property integer $game_id
 * @property string $player_id
 * @property string $player_job
 * @property integer $medals
 * @property integer $kills
 * @property integer $deaths
 * @property integer $assists
 * @property integer $damage
 * @property integer $healing
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
        'player_job',
        'medals',
        'kills',
        'deaths',
        'assists',
        'damage',
        'healing'
    ];

    public $hasOne = [
        'player' => [
            'Cleanse\League\Models\EventPlayer',
            'key' => 'id',
            'otherKey' => 'player_id'
        ]
    ];
}
