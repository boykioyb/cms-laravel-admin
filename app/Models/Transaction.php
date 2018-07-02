<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $connection = "porta-billing";
    protected $table = "linkgo_transactions";

    public function package(){
        return $this->hasOne(LinkGoPackage::class,'id','package_id');
    }

    public function account(){
        return $this->hasOne(Account::class, 'i_account','account_id');
    }
}
