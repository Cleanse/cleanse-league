<?php namespace Cleanse\League\Classes\Logo;

use Storage;
use System\Models\File;
use LasseRafn\InitialAvatarGenerator\InitialAvatar;
use Cleanse\League\Classes\Logo\UploadFile;

class DefaultLogoGenerator
{
    public $team;
    public $config;
    public $fileName;

    public function make($obj, $config = [])
    {
        $this->team = $obj;
        $this->config = $config;

        $fileName = $this->makeLogo();

//        $file = new File;
//        $file->data = $fileName;
//        $file->is_public = true;
//        $file->save();
//
//        return $file;
    }

    //move file
    //return file name and location
    public function makeLogo()
    {
        $tempLogo = $this->createTempFile();

        $this->moveLogoFile($tempLogo);
    }

    public function createTempFile()
    {
        $teamLogoFileName = $this->fileName = 'logo-team-' . $this->team->slug . '.png';

        $logo = $this->initialLogoGenerator();

        Storage::put($teamLogoFileName, $logo, 'public');
        $logoUrl = 'storage/app/' . $teamLogoFileName;

        return $logoUrl;
    }

    public function moveLogoFile($logo)
    {
        $uploadsDir = 'storage/app/uploads/public/' . $this->getPartitionDirectory();

        $file = new UploadFile($logo);

        $file->move($uploadsDir, $logo);
    }

    /**
     * Generates a partition for the file.
     * return /ABC/DE1/234 for an name of ABCDE1234.
     * @param Attachment $attachment
     * @param string $styleName
     * @return mixed
     */
    protected function getPartitionDirectory()
    {
        return implode('/', array_slice(str_split($this->fileName, 3), 0, 3)) . '/';
    }

    public function initialLogoGenerator()
    {
        $length = isset($this->config['length']) ? $this->config['length'] : 2;
        $size = isset($this->config['size']) ? $this->config['size'] : 128;
        $background = isset($this->config['background']) ? $this->config['background'] : '#010001';
        $color = isset($this->config['color']) ? $this->config['color'] : '#CFEBF8';

        $fontSize = .33; //(($size - 18) / $length) * .01;

        $logo = new InitialAvatar();

        return $logo->name($this->team->name)
            ->length($length)
            ->size($size)
            ->background($background)
            ->color($color)
            ->font('/fonts/OxygenMono-Regular.ttf')
            ->fontSize($fontSize)
            ->rounded()
            ->cache()
            ->generate()
            ->stream('png');
    }
}
