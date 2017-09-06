<?php namespace Cleanse\League\Models;

use Model;
use Storage;
use LasseRafn\Initials\Initials;
use LasseRafn\InitialAvatarGenerator\InitialAvatar;

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
        'event_teams' => 'Cleanse\League\Models\EventTeam',
        'players' => 'Cleanse\League\Models\Player'
    ];

    public function createDefaultInitials()
    {
        $initials = new Initials;

        return $initials->name($this->name)->length(3)->generate();
    }

    public function createDefaultLogo()
    {
        $teamLogoFileName = '/media/logo-team-' . $this->slug . '.png';

        $logo = $this->makeLogo();

        Storage::put($teamLogoFileName, $logo);
        $logoUrl = '/storage/app' . $teamLogoFileName;

        return $logoUrl;
    }

    public function makeLogo()
    {
        $logo = new InitialAvatar();

        return $logo->name($this->name)
            ->length(3)
            ->fontSize(0.5)
            ->size(400)
            ->background('#00CE97')
            ->color('#fff')
            ->cache()
            ->generate()
            ->stream('png');
    }
}
