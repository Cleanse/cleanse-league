<?php namespace Cleanse\League\Models;

use Model;

/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string $brackets
 * @property integer $winner_id
 */
class Tournament extends Model
{
    use \Cleanse\Urls\Classes\Shareable;
    use \October\Rain\Database\Traits\Sluggable;
    use \October\Rain\Database\Traits\Validation;

    public $table = 'cleanse_league_tournaments';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $jsonable = ['brackets'];

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

    /**
     * Relationships
     *
     * @var array
     */
    public $morphTo = [
        'tourneyable' => []
    ];

    public $morphMany = [
        'matches' => [
            'Cleanse\League\Models\Match',
            'name' => 'matchable'
        ],
        'teams' => [
            'Cleanse\League\Models\EventTeam',
            'name' => 'teamable'
        ]
    ];
}
