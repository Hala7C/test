<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Document\DisplayServices;


class Display extends Controller
{
    private DisplayServices $display_services;
    public function __construct(DisplayServices $display_services)
    {
        $this->display_services = $display_services;
    }



    public function myFiles(){
        $response=$this->display_services->myFiles();
        $data=$response['data'];
        $status =  $response['status'];
        return response()->json($data, $status);
        }




    public function myGroup(){
    $response=$this->display_services->myGroup();
    $data=$response['data'];
    $status =  $response['status'];
    return response()->json($data, $status);
    }




    public function documentsGroup($group_id){
    // $result=cache()->Cache::remember('group_documents',60*60, function () {});
    $response=$this->display_services->documentsGroup($group_id);
    $data=$response['data'];
    $status =  $response['status'];
    return response()->json($data, $status);
    }



    public function documentHisory($id){
        $response=$this->display_services->documentHisory($id);
        $data=$response['data'];
        $status =  $response['status'];
        return response()->json($data, $status);
    }
}
