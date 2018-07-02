<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\BatchAction;

class Evict extends BatchAction
{

    public function script()
    {
        $evictConfirm = trans('admin.evict_confirm');
        $confirm = trans('admin.confirm');
        $cancel = trans('admin.cancel');
        return <<<EOT
        
$('{$this->getElementClass()}').on('click', function(e) {
    e.preventDefault();
    swal({
      title: "$evictConfirm",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "$confirm",
      closeOnConfirm: true,
      cancelButtonText: "$cancel"
    },
    function(){
        $.ajax({
            method: 'post',
            url: './listphones/evict',
            data: {
                 ids: selectedRows(),
                _token:'{$this->getToken()}'
            },
            success: function (data) {
                    if (data) {
                        swal('Thu hồi số thành công', '', 'success');
                        $.pjax.reload('#pjax-container');
                    } else {
                        swal('Thu hồi số thất bại', '', 'error');
                        $.pjax.reload('#pjax-container');

                }
            }
        });
});
});

EOT;

    }
}
