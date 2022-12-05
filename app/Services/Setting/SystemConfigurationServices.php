<?php

namespace App\Services\Setting;


use App\Models\Document;
use App\Models\Edit_File;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Setting\SystemConfigurationRepository;
use Throwable;


class SystemConfigurationServices implements SystemConfigurationRepository
{
    public function getCurrentEnvValue(){
        $data=collect();
        $port=            env('DB_PORT');
        $url=             env('DATABASE_URL');
        $password=        env('DB_PASSWORD');
        $name=            env('DB_DATABASE');
        $username=        env('DB_USERNAME');
        $engine=          env('DB_ENGINE');
        $loglevel=        env('LOG_LEVEL');
        $allowed_uploads= env('COUNT_NO');
        $data->push([
            'url'=>$url,
            'port'=>$port,
            'db_username'=>$username,
            'db_password'=>$password,
            'db_name'=>$name,
            'db_engine'=>$engine,
            'log_level'=>$loglevel,
            'allowed_number_of_files'=>$allowed_uploads,
        ]);
        $status=210;
        return $response = ['data' => $data, 'status' => $status];
    }




    public function db_engine($request){

    }
    public function logLevel( $request){}
    public function db_connection( $request){}
    public function uploadsNo($key='COUNT_NO', $request, $delim=''){}
    public function updateDotEnvValue($key,$request, $delim=''){}
}
