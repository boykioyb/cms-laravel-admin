<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $connection = "porta-billing";
    protected $table = "Accounts";


}
