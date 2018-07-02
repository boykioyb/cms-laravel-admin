<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\AdminBuilder;

class LinkGoMobileProfile extends Model
{
    use  AdminBuilder;
    protected $connection = "porta-billing";
    protected $table = "linkgo_mobile_profiles";
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    public $fillable = ['mobile', 'package_id', 'expire_date'];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $mobile = new LinkGoMobile();
            $mobile->mobile = $model->mobile;
            $mobile->status = $model->status;
            $mobile->telco_code = $model->telco_code;
            $mobile->country_code = substr($model->mobile, 0, 2);
            $mobile->save();
            unset($model->telco_code);
        });

    }

    public function packages()
    {
        return $this->hasOne(LinkGoPackage::class, 'id', 'package_id');
    }

    public function mobiles()
    {
        return $this->hasOne(LinkGoMobile::class, 'mobile', 'mobile');
    }

    public function accounts()
    {
        return $this->hasOne(Account::class, 'i_account', 'account_id_parent');
    }

}
