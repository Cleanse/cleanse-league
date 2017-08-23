<?php namespace Cleanse\League\Models;

use Model;

/**
 * @property integer $id
 * @property integer $match_id
 * @property integer $team_one
 * @property integer $team_two
 * @property integer $winner_id
 */
class MatchGame extends Model
{
    public $table = 'cleanse_league_match_games';

    /***
     * Screenshot
     * @var array
     */
    public $attachOne = [
        'screenshot' => ['System\Models\File']
    ];
}
