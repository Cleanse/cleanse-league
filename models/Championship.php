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
    protected $fillable = ['name', 'slug', 'championship_rules'];

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

    public function currentSeason()
    {
        return $this->seasons()->whereNull('finished_at')->first();
    }
}
