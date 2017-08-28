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

    /**
     * @param $data
     */
    public function storeFormData($data)
    {
        $name_field_value = NULL;
        $slug_field_value = NULL;
        $about_field_value = NULL;

        foreach ($data as $key => $value) {
            if (substr($key, 0, 1) == '_') {
                continue;
            }
            $output[$key] = e($value['value']);

            if (empty($name_field_value) and $key == 'name') {
                $name_field_value = e($value['value']);
            }

            if (empty($slug_field_value) and $key == 'slug') {
                $slug_field_value = e($value['value']);
            }

            if (empty($about_field_value) and $key == 'about') {
                $about_field_value = e($value['value']);
            }
        }

        $this->name = $name_field_value;
        $this->slug = $slug_field_value;
        $this->about = $about_field_value;
        $this->save();
    }
}
