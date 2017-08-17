<?php namespace Cleanse\League\Models;

use Markdown;
use Model;
use Str;

/**
 * @property int $id
 * @property int $winner_id
 * @property int $team_a
 * @property int $team_b
 * @property int $team_a_score
 * @property int $team_b_score
 */
class TeamStats extends Model
{
    public $table = 'cleanse_league_teams_stats';
}
