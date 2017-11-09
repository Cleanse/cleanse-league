<?php namespace Cleanse\League\Models;

use Model;
use Cleanse\League\Classes\Stats\PlayerStats;
use Cleanse\League\Models\Season;

/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string $avatar
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
        'avatar' => ['System\Models\File']
    ];

    /**
     * @var array
     */
    public $hasMany = [
        'players' => 'Cleanse\League\Models\EventPlayer'
    ];

    public $belongsTo = [
        'team' => 'Cleanse\League\Models\Team'
    ];

    /**
     * @param int $size
     * @param null $options
     * @return string
     */
    public function getAvatarThumb($size = 48, $options = null)
    {
        if (is_string($options)) {
            $options = ['default' => $options];
        } elseif (!is_array($options)) {
            $options = [];
        }

        if ($this->avatar) {
            return $this->avatar->getThumb($size, $size, $options);
        } else {
            return '/themes/pvpaissa/assets/images/default-paissa.jpg';
        }
    }

    public function getLifetimeStats(){}

    /**
     * @return array
     */
    public function getSeasonStats()
    {
        if (!$this->players()->count() > 0) {
            return [];
        }

        $currentSeason = Season::orderBy('id', 'desc')
            ->first();

        $seasonPlayer = $this->players()->where('playerable_type', '=', 'season')
            ->where('playerable_id', '=', $currentSeason->id)
            ->first();

        if (!isset($seasonPlayer)) {
            return [];
        }

        $stats = new PlayerStats($seasonPlayer);

        return $stats->seasonal();
    }
}
