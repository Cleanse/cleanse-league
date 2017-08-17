<?php namespace Cleanse\League\Models;

use Db;
use Model;

/**
 * @property string $id
 * @property string $name
 * @property string $slug
 *
 * Each tournament has many teams, and each team can be part of many tournaments (n:n).
 * Each team has many matches, and each match has two teams (n:n).
 */
class Team extends Model
{
    use \Cleanse\Urls\Classes\Shareable;
    use \October\Rain\Database\Traits\Sluggable;
    use \October\Rain\Database\Traits\Validation;

    public $table = 'cleanse_league_teams';
    protected $primaryKey = 'id';
    public $incrementing = false;

    /**
     * @var array Generate shareable string for primary key.
     */
    protected $shareable = ['id' => ['default']];

    /**
     * @var array Generate slugs for these attributes.
     */
    protected $slugs = ['slug' => 'name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /*
     * Validation
     */
    public $rules = [
        'name' => 'required'
    ];

    /***
     * Logo
     * @var array
     */
    public $attachOne = [
        'logo' => ['System\Models\File']
    ];

    /**
     * Relationships
     */
    public $hasMany = [
        'event_teams' => 'Cleanse\League\Models\EventTeam'
    ];
}
