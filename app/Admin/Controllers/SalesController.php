<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ExcelExporter;

use App\Models\Transaction;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
class SalesController extends Controller
{
    use ModelForm;

    protected function index()
    {
        return Admin::content(function (Content $content) {
            $content->header(trans('title.sales'));
            $content->description(trans('title.list'));
            $content->body($this->grid());
        });
    }

    protected function grid()
    {
        return Admin::grid(Transaction::class, function (Grid $grid) {

            $grid->paginate(20);

            $grid->id('ID')->sortable();

            $grid->column('created', trans('translate.created'))->sortable();
            $grid->column('modified', trans('translate.modified'))->sortable();

            $grid->column('account.id', 'User');
            $grid->mobile(trans('translate.linkgo_number'));

            $grid->type(trans('translate.type'));
            $grid->method(trans('translate.method'));

            $grid->amount(trans('translate.amount'))->display(function ($number) {
                return number_format($number, '0', '', '.');
            })->sortable();

            $grid->filter(function (Grid\Filter $filter) {

                $filter->between('created', trans('translate.created'))->datetime();
                $filter->between('modified', trans('translate.modified'))->datetime();

                $filter->equal('account.id', 'User');
                $filter->equal('mobile', trans('translate.linkgo_number'));

                $filter->equal('type', trans('translate.type'));
                $filter->equal('method', trans('translate.method'));

            });
            $export = new ExcelExporter();
            $export->setFileName('sales_' . date('d_m_Y_His'));
            $export->setSheetName('Doanh Thu');
            $export->setTitles(['ID', 'Thời gian tạo', 'Thời gian sửa', 'User', 'Số LinkGo', 'Loại giao dịch', 'Phương thức thanh toán', 'Giá']);
            $export->setFields(['id', 'created', 'modified', 'account.id', 'mobile', 'type', 'method', 'amount']);

            $grid->exporter($export);
            $grid->disableRowSelector();
            $grid->disableCreateButton();
            $grid->disableActions();


        });
    }


}