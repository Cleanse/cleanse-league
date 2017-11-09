<?php namespace Cleanse\League\Models;

use Model;
use Cleanse\League\Classes\Stats\TeamStats;

/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string $initials
 * @property string $logo
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
    protected $fillable = ['name', 'initials'];

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
        'championship_teams' => 'Cleanse\League\Models\ChampionshipTeam',
        'event_teams' => 'Cleanse\League\Models\EventTeam',
        'players' => 'Cleanse\League\Models\Player'
    ];

    public $morphMany = [
        'logs' => [
            'Cleanse\League\Models\ManagerLog',
            'name' => 'loggable'
        ]
    ];

    //if no logo
    public function getLogoThumb($size = 48, $options = null)
    {
        if (is_string($options)) {
            $options = ['default' => $options];
        } elseif (!is_array($options)) {
            $options = [];
        }

        if ($this->logo) {
            return $this->logo->getThumb($size, $size, $options);
        } else {
            return '/themes/pvpaissa/assets/images/default-paissa.jpg';
        }
    }

    /**
     * @return array
     */
    public function getLifetimeStats()
    {
        if (!$this->event_teams()->count() > 0) {
            return [];
        }

        $stats = new TeamStats($this->players);

        return $stats->seasonal();
    }

    public function getSeasonStats()
    {
        if (!$this->event_teams()->count() > 0) {
            return [];
        }

        $players = [];

        $stats = new TeamStats($this->players);

        return $stats->seasonal();
    }
}
