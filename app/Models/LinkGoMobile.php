<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkGoMobile extends Model
{
    protected $connection = "porta-billing";
    protected $table = "linkgo_mobiles";
    public $timestamps = false;

    public function mobileProfiles()
    {
        return $this->belongsTo(LinkGoMobileProfile::class, 'mobile', 'mobile');
    }

}
