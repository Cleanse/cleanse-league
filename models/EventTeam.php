<?php namespace Cleanse\League\Models;

use Db;
use Model;

/**
 * @property integer $id
 * @property integer $team_id
 * @property integer $event_id
 */
class EventTeam extends Model
{
    public $table = 'cleanse_league_event_teams';

    /**
     * Relationships
     */
    public $morphTo = [
        'teamable' => []
    ];

    public $belongsTo = [
        'team' => 'Cleanse\League\Models\Team'
    ];
}
