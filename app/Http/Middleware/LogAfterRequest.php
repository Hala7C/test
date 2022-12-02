<?php
namespace App\Http\Middleware;

use App\Models\Client_History;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
class LogAfterRequest {

	public function handle($request, \Closure  $next)
	{
		return $next($request);
	}

	public function terminate( $request,  $response)
	{
		// Log::info('app.requests', ['request' => $request->all(), 'response' => $response]);
            Client_History::create([
                'ip' =>$request->ip(),
                'status' => $response->getStatusCode(),
                'method' => $request->method(),
                'uri' =>  $request->fullUrl(),
                'body' =>json_encode( $request->all()),
                'header' =>json_encode( $request->header()),
                'response' => $response->content(),
                        ]);
	}

}
