<?php namespace Cleanse\League\Classes\ManagerLog;

use Cleanse\League\Models\ManagerLog;

class LeagueHandler
{
    public function handle($admin, $method, $data)
    {
        $toCollection = collect($data);

        $log = new ManagerLog();

        $log->admin_id = $admin->id;
        $log->ip = $this->getIp();
        $log->method = $method;
        $log->values = $toCollection->toJson();

        $log->save();
    }

    private function getIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }

        return $ip_address;
    }
}
