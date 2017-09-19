<?php namespace Cleanse\League\Models;

use Model;

/**
 * @property integer $id
 * @property integer $player_id
 * @property integer $event_team_id
 * @property string $jobs
 * @property integer $kills
 * @property integer $deaths
 * @property integer $assists
 * @property integer $damage
 * @property integer $healing
 * @property integer $medals
 * @property integer $match_total
 * @property integer $match_wins
 * @property integer $match_losses
 * @property integer $game_total
 * @property integer $game_wins
 * @property integer $game_losses
 * @property integer $game_ties
 * @property string $playerable_id
 * @property string $playerable_type
 */
class EventPlayer extends Model
{
    public $table = 'cleanse_league_event_players';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['player_id', 'event_team_id'];

    /**
     * Relationships
     */
    public $morphTo = [
        'playerable' => []
    ];

    public $belongsTo = [
        'player' => 'Cleanse\League\Models\Player'
    ];
}
