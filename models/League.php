<?php namespace Cleanse\League\Models;

use Markdown;
use Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property string $about
 * @property string $about_html
 */
class League extends Model
{
    use \October\Rain\Database\Traits\Sluggable;
    use \October\Rain\Database\Traits\Validation;

    public $table = 'cleanse_league_leagues';

    /*
     * Validation
     */
    public $rules = [
        'name' => 'required'
    ];

    /*
     * Relations
     */
    public $hasMany = [
        'championships' => 'Cleanse\League\Models\Championship'
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
        $this->about_html = Markdown::parse(trim($this->about));
    }
}
