<?php namespace Cleanse\League\Models;

use Model;

/**
 * @property integer $id
 * @property integer $team_id
 * @property integer $event_id
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
