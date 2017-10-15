<?php namespace Cleanse\League\Models;

use Model;

/**
 * @property integer $id
 * @property integer $championship_id
 * @property integer $team_id
 * @property integer $points
 */
class ChampionshipTeam extends Model
{
    public $table = 'cleanse_league_championship_teams';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['championship_id', 'team_id', 'points'];

    /**
     * Relationships
     */
    public $belongsTo = [
        'team' => 'Cleanse\League\Models\Team'
    ];

    /**
     * Create ChampTeam
     * Get ChampTeam id
     * Create PointSource with ChampTeam id
     * @var array
     */
    public $hasMany = [
        'sources' => 'Cleanse\League\Models\ChampionshipPointSource'
    ];
}
