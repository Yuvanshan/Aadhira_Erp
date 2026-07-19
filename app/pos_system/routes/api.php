<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/discover', function () {
    try {
        $business = \App\Business::orderBy('id', 'asc')->first();
        $client = \DB::table('oauth_clients')->where('password_client', 1)->first();
        $business_name = $business ? $business->name : 'Aadhira ERP';
        $client_id = $client ? (string)$client->id : '2';
        $client_secret = $client ? $client->secret : '79GOzjI3O2Iv5F1Kkp8gHMH7CUdZGeULzIDX5WQM';
    } catch (\Exception $e) {
        $business_name = 'Aadhira ERP';
        $client_id = '2';
        $client_secret = '79GOzjI3O2Iv5F1Kkp8gHMH7CUdZGeULzIDX5WQM';
    }

    return response()->json([
        'status' => 'success',
        'app' => 'aadhira_erp',
        'business_name' => $business_name,
        'server_url' => request()->getSchemeAndHttpHost(),
        'client_id' => $client_id,
        'client_secret' => $client_secret,
    ]);
});
