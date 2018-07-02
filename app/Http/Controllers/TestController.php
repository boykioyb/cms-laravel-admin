<?php

namespace App\Http\Controllers;


class TestController extends Controller
{
    public function index()
    {
        $data = array(
            'success' => true,
            'data' => [
                'https://kenh14cdn.com/2017/212310618041193431006884986278710988847462n-1507458620893.jpg',
                'https://kenh14cdn.com/2017/212310618041193431006884986278710988847462n-1507458620893.jpg',
                'https://kenh14cdn.com/2017/212310618041193431006884986278710988847462n-1507458620893.jpg',
                'https://kenh14cdn.com/2017/212310618041193431006884986278710988847462n-1507458620893.jpg',
                'https://kenh14cdn.com/2017/212310618041193431006884986278710988847462n-1507458620893.jpg',
                'https://kenh14cdn.com/2017/212310618041193431006884986278710988847462n-1507458620893.jpg',
                'https://kenh14cdn.com/2017/212310618041193431006884986278710988847462n-1507458620893.jpg',
                'https://kenh14cdn.com/2017/212310618041193431006884986278710988847462n-1507458620893.jpg',
            ]
        );
        return response($data);
    }
}