<?php namespace Cleanse\League\Classes\ManagerLog;

use Cleanse\League\Models\ManagerLog;

class ReadJson
{
    public static function toArray($json)
    {
        $logData = json_decode($json, true);

        return $logData;
    }
}
