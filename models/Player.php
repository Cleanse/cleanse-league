<?php namespace Cleanse\League\Models;

use DB;
use Model;

/**
 * @property string $id
 * @property string $name
 * @property string $slug
 */
class Player extends Model
{
    use \Cleanse\Urls\Classes\Shareable;
    use \October\Rain\Database\Traits\Sluggable;
    use \October\Rain\Database\Traits\Validation;

    public $table = 'cleanse_league_players';
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
     * Avatar
     * @var array
     */
    public $attachOne = [
        'logo' => ['System\Models\File']
    ];
}
