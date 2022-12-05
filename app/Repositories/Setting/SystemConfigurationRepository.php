<?php

namespace App\Repositories\Setting;

interface SystemConfigurationRepository
{
    public function getCurrentEnvValue();
    public function db_engine($request);
    public function logLevel( $request);
    public function db_connection( $request);
    public function uploadsNo($key='COUNT_NO', $request, $delim='');
    public function updateDotEnvValue($key,$request, $delim='');

}
