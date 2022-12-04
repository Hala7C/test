<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SystemConfiguration extends Controller
{
    public function db_engine(){

    }
    public function db_connection(Request $request){
        /*
        My advise for all that have this problem, is use a double db connection, one for the main db (server db) and one for customer db. In this way you can switch to both db, with this simple code:
        Config::set("database.default", "sqlsrvCustomer");
        \Illuminate\Support\Facades\DB::reconnect();
        With the first command you can choose the customer DB and with the second you can connect to it.
        */


        $data=collect();
        $this->validate($request,[
            'url'=>'string',
            'port'=>'numeric',
            'db_username'=>'string',
            'db_password'=>'string',
            'db_name'=>'string',
        ]);
        DB::disconnect();
        config(['database.connections.mysql.url' => $request->url]);
        config(['database.connections.mysql.port' => $request->port]);
        config(['database.connections.mysql.username' => $request->db_username]);
        config(['database.connections.mysql.password' => $request->db_password]);
        config(['database.connections.mysql.database' => $request->db_name]);
        $port=       Config::get('database.connections.mysql.port');
        $username=   Config::get('database.connections.mysql.username');
        $url=        Config::get('database.connections.mysql.url');
        $password=   Config::get('database.connections.mysql.password');
        $driver=     Config::get('database.connections.mysql.driver');
        $name=       Config::get('database.connections.mysql.database');

        DB::purge('mysql');
        DB::reconnect();
        $data->push([
            'url'=>$url,
            'port'=>$port,
            'db_username'=>$username,
            'db_password'=>$password,
            'driver'=>$driver,
            'db_name'=>$name,
        ]);
        return response()->json(['data'=> $data],210);
    }
    public function document_rate(){

    }
}
