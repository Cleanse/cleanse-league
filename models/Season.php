<?php namespace Cleanse\League\Models;

use Model;
use Cleanse\League\Models\EventTeam;

/**
 * @property integer $id
 * @property integer $championship_id
 * @property integer $winner_id
 * @property string $name
 * @property string $slug
 * @property string $finished_at
 */
class Season extends Model
{
    use \October\Rain\Database\Traits\Sluggable;
    use \October\Rain\Database\Traits\Validation;

    public $table = 'cleanse_league_seasons';

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
     * @var array Generate a slug for this attribute.
     */
    protected $slugs = ['slug' => 'name'];

    public $belongsTo = [
        'championship' => 'Cleanse\League\Models\Championship'
    ];

    /**
     * Get all of the tourneys, matches.
     */
    public $morphMany = [
        'tourneys' => [
            'Cleanse\League\Models\Tournament',
            'name' => 'tourneyable'
        ],
        'matches' => [
            'Cleanse\League\Models\Match',
            'name' => 'matchable'
        ],
        'teams' => [
            'Cleanse\League\Models\EventTeam',
            'name' => 'teamable'
        ],
        'players' => [
            'Cleanse\League\Models\EventPlayer',
            'name' => 'playerable'
        ],
        'logs' => [
            'Cleanse\League\Models\ManagerLog',
            'name' => 'loggable'
        ]
    ];

    public function storeFormData($data)
    {
        $name_field_value = NULL;

        foreach ($data as $key => $value) {
            if (substr($key, 0, 1) == '_') {
                continue;
            }
            $output[$key] = e($value['value']);

            if (empty($name_field_value) and $key == 'name') {
                $name_field_value = e($value['value']);
            }
        }

        $this->name = $name_field_value;
        $this->save();
    }

    public function winner()
    {
        $winner = EventTeam::find($this->winner_id);

        return $winner;
    }
}
