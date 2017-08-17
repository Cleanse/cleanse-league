<?php namespace Cleanse\League\Models;

use Db;
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
     * Relationships
     */
    public $morphTo = [
        'playerable' => []
    ];
}
