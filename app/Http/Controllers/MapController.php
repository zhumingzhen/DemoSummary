<?php
/**
 * Created by PhpStorm.
 * User: z@it1.me
 * Date: 2017/12/31 0031
 * Time: 下午 22:25
 */

namespace App\Http\Controllers;

use GuzzleHttp\Client;


class MapController
{
    public function yingyanSearch()
    {

        $http = new Client();
        $ak = env('MAP_AK');
        $service_id = env('SERVICE_ID');
        $url = "http://yingyan.baidu.com/api/v3/entity/search?ak=$ak&service_id=$service_id";
        $response = $http->get($url);
        $data = json_decode((string)$response->getBody(), true);
        dd($data);
    }
}