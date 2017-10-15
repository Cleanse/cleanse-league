<?php namespace Cleanse\League\Models;

use Model;

/**
 * Class ManagerLog
 * @package Cleanse\League\Models
 *
 * @property integer $id
 * @property integer $admin_id
 * @property string $ip
 * @property string $method
 * @property string $values
 */
class ManagerLog extends Model
{
    public $table = 'cleanse_league_manager_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['admin_id', 'ip', 'method', 'values'];
}
