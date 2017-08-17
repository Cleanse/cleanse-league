<?php namespace Cleanse\League\Models;

use Markdown;
use Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property string $information
 * @property string $information_html
 */
class Event extends Model
{
    use \October\Rain\Database\Traits\Sluggable;
    use \October\Rain\Database\Traits\Validation;

    public $table = 'cleanse_league_events';

    /*
     * Validation
     */
    public $rules = [
        'name' => 'required'
    ];

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
        ]
    ];

    /**
     * @var array Generate a slug for this attribute.
     */
    protected $slugs = ['slug' => 'name'];

    /**
     * Create html versions out of these fields.
     */
    public function beforeSave()
    {
        $this->information_html = Markdown::parse(trim($this->information));
    }

    /**
     * @param $data
     */
    public function storeFormData($data)
    {
        $name_field_value = NULL;
        $information_field_value = NULL;

        foreach ($data as $key => $value) {
            if (substr($key, 0, 1) == '_') {
                continue;
            }
            $output[$key] = e($value['value']);

            if (empty($name_field_value) and $key == 'name') {
                $name_field_value = e($value['value']);
            }

            if (empty($information_field_value) and $key == 'information') {
                $information_field_value = e($value['value']);
            }
        }

        $this->name = $name_field_value;
        $this->information = $information_field_value;
    }
}
