<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SystemConfiguration extends Controller
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
        return response()->json(['data'=>$data],210);
    }
    public function db_engine(Request $request){
        $engines=array('innodb','myisam','memory','merge','example','csv','aria','mrg_myisam','sequence','performance_schema');
        $validator = Validator::make($request->all(), [
            'engine'=>'string|in:' . implode(',', $engines),
        ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }
            if(isset($request->engine)){
                $this->updateDotEnvValue('DB_ENGINE', $request->engine);
                $data=collect();
                $data->push([
                    'engine'=> $request->engine,
                ]);
            }


            return response($data,210);
    }
    public function logLevel(Request $request){
        $levels=array('debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency');
        $validator = Validator::make($request->all(), [
            'level'=>'string|in:' . implode(',', $levels),
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
            if(isset($request->level)){
                $this->updateDotEnvValue('LOG_LEVEL', $request->level); $data=collect();
                $data->push([
                    'level'=> $request->level,
                ]);
            }


            return response($data,210);
    }
    public function db_connection(Request $request){
        $data=collect();
        $this->validate($request,[
            'url'=>'string',
            'port'=>'numeric',
            'db_username'=>'string',
            'db_password'=>'string',
            'db_name'=>'string',
        ]);
        DB::disconnect();
        if(isset($request->url)){
            $this->updateDotEnvValue('DATABASE_URL', $request->url);
        }
        if(isset( $request->port)){
            $this->updateDotEnvValue('DB_PORT', $request->port);
        }
        if(isset($request->db_username)){
            $this->updateDotEnvValue('DB_USERNAME', $request->db_username);
        }
        if(isset( $request->db_password)){
            $this->updateDotEnvValue('DB_PASSWORD', $request->db_password);
        }
        if(isset( $request->db_name)){
            $this->updateDotEnvValue('DB_DATABASE', $request->db_name);
        }
        DB::purge('mysql');
        DB::reconnect();
        $data=collect();
        $data->push([
            'url'=> env('DATABASE_URL'),
            'port'=> env('DB_PORT'),
            'db_username'=> env('DB_USERNAME'),
            'db_password'=> env('DB_PASSWORD'),
            'db_name'=> env('DB_DATABASE'),
        ]);
        return response($data,210);

    }
    protected function uploadsNo($key='COUNT_NO',Request $request, $delim='')
{

    $path = base_path('.env');
    // get old value from current env
    $oldValue = env($key);

    // was there any change?
    if ($oldValue === $request->number) {
        return;
    }

    // rewrite file content with changed data
    if (file_exists($path)) {
        // replace current value with new value
        file_put_contents(
            $path, str_replace(
                $key.'='.$delim.$oldValue.$delim,
                $key.'='.$delim.$request->number.$delim,
                file_get_contents($path)
            )
        );
    }
    $data=collect();
    $data->push([
        'allowed_number_of_files'=> $request->number,
    ]);
    return response($data,210);
}

protected function updateDotEnvValue($key,$request, $delim='')
{

    $path = base_path('.env');
    // get old value from current env
    $oldValue = env($key);

    // was there any change?
    if ($oldValue === $request) {
        return;
    }

    // rewrite file content with changed data
    if (file_exists($path)) {
        // replace current value with new value
        file_put_contents(
            $path, str_replace(
                $key.'='.$delim.$oldValue.$delim,
                $key.'='.$delim.$request.$delim,
                file_get_contents($path)
            )
        );
    }

}
}
