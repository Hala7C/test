<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SystemConfiguration extends Controller
{
    public function db_engine(){

    }
    public function db_connection(Request $request){
        $this->validate($request,[
            'db_name'=>'string',
            'port'=>'numeric',
            'credential'=>'',
        ]);
        // config(['database.database' => $request->db_name]);
        config(['database.connections.mysql.port' => $request->port]);
        // config(['database.database' => $request->db_name]);
    }
    public function document_rate(){

    }
}
