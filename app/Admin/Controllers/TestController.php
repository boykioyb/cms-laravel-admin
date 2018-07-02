<?php

namespace App\Admin\Controllers;


use App\Models\LinkGoMobile;
use App\Models\LinkGoMobileProfile;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    public function index(){
        $mobiles = new LinkGoMobile();
        $profile = new LinkGoMobileProfile();
        $profile->mobile = '84979008320';
        $mobiles->mobile = '84979008320';
        $profile->mobiles()->save();
    }
}