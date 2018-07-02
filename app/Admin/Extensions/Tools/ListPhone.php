<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\AbstractTool;

class ListPhone extends AbstractTool
{
    public function render()
    {
        return view('admin.tools.list-phone-button');
    }
}