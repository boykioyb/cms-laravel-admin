<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\AbstractTool;

class ButtonGenAuto extends AbstractTool
{
    public function render()
    {
        return "<a href='./permissions/autogenerate' class='btn btn-sm btn-success'><i class='fa fa-refresh'></i> Tự động tạo quyền hạn</a>";
    }
}