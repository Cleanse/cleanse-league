<?php namespace Cleanse\League\Models;

use Model;

/**
 * Class ChampionshipPointSource
 * @package Cleanse\League\Models
 *
 * @property integer $id
 * @property integer $championship_team_id
 * @property integer $championship_id
 * @property string $team_id
 * @property string $source
 * @property integer $value
 * @property string $comment
 */
class ChampionshipPointSource extends Model
{
    public $table = 'cleanse_league_championship_points';
}
