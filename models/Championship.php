<?php namespace Cleanse\League\Models;

use Markdown;
use Model;
use Str;
use RainLab\Blog\Classes\TagProcessor;

/**
 * @property integer $id
 * @property integer $league_id
 * @property integer $winner_id
 * @property string $name
 * @property string $slug
 * @property string $championship_rules
 * @property string $rules_html
 * @property string $finished_at
 */
class Championship extends Model
{
    use \October\Rain\Database\Traits\Sluggable;
    use \October\Rain\Database\Traits\Validation;

    public $table = 'cleanse_league_championships';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['championship_rules'];

    /*
     * Validation
     */
    public $rules = [
        'name' => 'required'
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'league' => ['Cleanse\League\Models\League']
    ];

    public $hasMany = [
        'seasons' => ['Cleanse\League\Models\Season']
    ];

    public $morphMany = [
        'tourneys' => [
            'Cleanse\League\Models\Tournament',
            'name' => 'tourneyable'
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

    public function beforeSave()
    {
        $this->rules_html = self::formatHtml($this->championship_rules);
    }

    public static function formatHtml($input, $preview = false)
    {
        $result = Markdown::parse(trim($input));

        $result = TagProcessor::instance()->processTags($result, $preview);

        return $result;
    }

    public function storeFormData($data)
    {
        $name_field_value = NULL;
        $rules_field_value = NULL;

        foreach ($data as $key => $value) {
            if (substr($key, 0, 1) == '_') {
                continue;
            }
            $output[$key] = e($value['value']);

            if (empty($name_field_value) and $key == 'name') {
                $name_field_value = e($value['value']);
            }

            if (empty($rules_field_value) and $key == 'rules') {
                $rules_field_value = e($value['value']);
            }
        }

        $this->name = $name_field_value;
        $this->championship_rules = $rules_field_value;
        $this->save();
    }
}
