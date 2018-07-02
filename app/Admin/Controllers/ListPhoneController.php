<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ExcelExporter;
use App\Admin\Extensions\Tools\Evict;
use App\Admin\Extensions\Tools\ListPhone;
use App\Models\Account;
use App\Models\LinkGoMobile;
use App\Models\LinkGoMobileProfile;
use App\Models\LinkGoPackage;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\MessageBag;
use Maatwebsite\Excel\Facades\Excel;

class ListPhoneController extends Controller
{
    use ModelForm;
    protected $package;
    protected $mobile;
    protected $mobileProfile;
    protected $request;
    protected $account;

    protected static $STATUS_INACTIVE = 0;

    protected $telco = array('VINAPHONE' => 'VINAPHONE', 'MOBIFONE' => 'MOBIFONE', 'VIETTEL' => 'VIETTEL');
    protected $status = array('active', 'inactive');

    public function __construct(LinkGoPackage $linkGoPackage, LinkGoMobile $linkGoMobile,
                                LinkGoMobileProfile $linkGoMobileProfile, Account $account,
                                Request $request)
    {
        $this->package = $linkGoPackage;
        $this->mobile = $linkGoMobile;
        $this->mobileProfile = $linkGoMobileProfile;
        $this->account = $account;

        $this->request = $request;
    }

    protected function index()
    {
        return Admin::content(function (Content $content) {
            $content->header(trans('title.list_phone'));
            $content->description(trans('title.list'));
            $content->body($this->grid());
        });
    }

    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('Users');
            $content->description('edit');

            $content->body($this->form()->edit($id));
        });
    }

    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header(trans('title.list_phone'));
            $content->description('Thêm mới');

            $content->body($this->form());
        });
    }

    protected function grid()
    {
        return Admin::grid(LinkGoMobileProfile::class, function (Grid $grid) {


            $grid->paginate(20);

            $grid->id('ID')->sortable();

            $grid->mobile(trans('translate.phone'));

            $grid->column('mobiles.telco_code', trans('translate.telco_code'));

            $grid->column('packages.name', trans('translate.package_name'));

            $grid->column('packages.price', trans('translate.package_price'))->display(function ($number) {
                return number_format($number, '0', '', '.');
            });

            $grid->column('accounts.id', 'User');

            $grid->status(trans('translate.status'))->display(function ($status) {
                if ($status == 1) {
                    return '<span class="label label-success">Active</span>';
                } else {
                    return '<span class="label label-danger">Inactive</span>';
                }
            });;

            $grid->expire_date(trans('translate.expire_date'));

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('mobile', trans('translate.linkgo_number'));
                $filter->equal('mobiles.telco_code', trans('translate.telco_code'))->select($this->telco);
                $filter->equal('status', trans('translate.status'))->select($this->status);
                $filter->equal('packages.name', trans('translate.package_name'))->select(
                    $this->package::all()->pluck('name', 'id')
                );
            });
            $grid->tools(function ($tools) {
                $tools->append(new ListPhone());
                $tools->batch(function (Grid\Tools\BatchActions $batch) {
                    $batch->add('Thu hồi số', new Evict());
                });
            });

            $export = new ExcelExporter();
            $export->setFileName('danh_sach_so_' . date('d_m_Y_His'));
            $export->setSheetName('Danh sách số');
            $export->setTitles(['ID', 'Số điện thoại', 'Nhà mạng', 'Tên gói', 'Giá', 'User', 'Trạng thái', 'Hạn sử dụng']);
            $export->setFields(['id', 'mobile', 'mobiles.telco_code', 'packages.name', 'packages.price', 'accounts.id', 'status', 'expire_date']);

            $grid->exporter($export);

            $grid->disableCreateButton();
            $grid->disableActions();
        });
    }

    public function form()
    {
        return LinkGoMobileProfile::form(function (Form $form) {

            $form->setTitle('Thêm mới số LinkGo');

            $form->hidden('status')->default(1);

            $form->mobile('mobile', trans('translate.linkgo_number'))->attribute('id', 'mobileGen')->rules(array(
                'required',
                'min:10',
                'max:11',
                'regex:/^(0|\+84|84)(1[2689]|9|8)[0-9]{8}$/'),
                [
                    'required' => 'Số LinkGo không được để trống',
                    'min' => 'Hãy nhập đúng số điện thoại, ít nhất là 10 số',
                    'regex' => 'Số điện thoại bạn nhập không đúng. Xin vui lòng nhập lại'
                ]);

            $form->select('telco_code', trans('translate.telco_code'))->options($this->telco);

            $form->select('package_id', trans('translate.package_name'))->options(
                $this->package::all()->pluck('description', 'id')
            );

            $form->hidden('register_date');

            $form->datetime('expire_date')->rules('required', [
                'required' => 'Hãy nhập ngày hết hạn'
            ]);

            $form->saving(function (Form $form) {
                $form->register_date = $form->expire_date;
            });

        });
    }


    public function formImport()
    {
        return Admin::content(function (Content $content) {

            $content->header(trans('title.list_phone'));
            $content->description('Thêm mới');

            $content->body(view('admin.listPhones.import'));
        });
    }

    public function saveImport()
    {
//validate the xls file
        if ($this->request->hasFile('file')) {
            $extension = File::extension($this->request->file->getClientOriginalName());

            if ($extension == "xlsx" || $extension == "xls" || $extension == "csv") {

                $path = $this->request->file->getRealPath();

                $data = Excel::load($path, function ($reader) {
                })->get();
                $profiles = $mobile = array();
                if (!empty($data) && $data->count()) {
                    foreach ($data as $key => $value) {
                        $findNamePackageGetId = !empty($value->ten_goi)
                            ? $this->package->where('name', $value->ten_goi)->first() : '';

                        $findIdAccountgetIAccount = !empty($value->user) ?
                            $this->account->where('id', (string)$value->user)->first()->i_account : '';

                        $profiles[] = [
                            'mobile' => !empty($value->so_dien_thoai) ? $value->so_dien_thoai : '',
                            'expire_date' => !empty($value->han_su_dung) ? $value->han_su_dung : '',
                            'register_date' => !empty($value->han_su_dung) ? $value->han_su_dung : '',
                            'status' => !empty($value->trang_thai) ? (int)$value->trang_thai : 1,
                            'package_id' => $findNamePackageGetId->id,
                            'account_id_parent' => $findIdAccountgetIAccount,
                            'created' => date('Y-m-d H:i:s'),
                            'modified' => date('Y-m-d H:i:s'),

                        ];
                        $mobile[] = [
                            'mobile' => !empty($value->so_dien_thoai) ? $value->so_dien_thoai : '',
                            'status' => !empty($value->trang_thai) ? (int)$value->trang_thai : 1,
                            'country_code' => substr($value->so_dien_thoai, 0, 2),
                            'telco_code' => !empty($value->nha_mang) ? $value->nha_mang : ''
                        ];
                    }

                    if (!empty($profiles)) {
                        DB::table('porta_billing.linkgo_mobile_profiles')->insert($profiles);
                    }
                    if (!empty($mobile)) {
                        DB::table('porta_billing.linkgo_mobiles')->insert($mobile);
                    }
                }
                admin_toastr('Import danh sách số thành công');
                return redirect(route('listphones.index'));

            } else {
                $error = new MessageBag([
                    'title' => 'Error',
                    'message' => 'File is a ' . $extension . ' file.!! Please upload a valid xls/csv file..!!',
                ]);
                return back()->with(compact('error'));
            }
        }
    }


    public function formImportEvict()
    {
        return Admin::content(function (Content $content) {

            $content->header(trans('title.list_phone'));
            $content->description('Thêm mới');

            $content->body(view('admin.listPhones.import-evict'));
        });
    }

    public function saveImportEvict()
    {
//validate the xls file
        if ($this->request->hasFile('file')) {
            $extension = File::extension($this->request->file->getClientOriginalName());

            if ($extension == "xlsx" || $extension == "xls" || $extension == "csv") {

                $path = $this->request->file->getRealPath();

                $data = Excel::load($path, function ($reader) {
                })->get();

                if (!empty($data) && $data->count()) {
                    foreach ($data as $key => $value) {
                        $this->_updateStatus($this->mobileProfile, $value->so_dien_thoai);
                        $this->_updateStatus($this->mobile, $value->so_dien_thoai);
                    }
                }
                admin_toastr('Import danh sách thu hồi số thành công');
                return redirect(route('listphones.index'));

            } else {
                $error = new MessageBag([
                    'title' => 'Error',
                    'message' => 'File is a ' . $extension . ' file.!! Please upload a valid xls/csv file..!!',
                ]);
                return back()->with(compact('error'));
            }
        }
    }

    public function evict()
    {
        $flag = false;
        $data = $this->request->get('ids');
        if (!empty($data)) {
            foreach ($data as $value) {
                $flag = $this->_updateStatusById($this->mobileProfile, $value);
            }
        }
        return response(json_encode($flag));
    }

    protected function _updateStatusById($model, $value)
    {
        $data = $model->where('id', $value)->first();
        if (!empty($data)) {
            $data->status = self::$STATUS_INACTIVE;
            $data->save();
            $findMobile = $this->mobile->where('mobile', $data->mobile)->first();
            if (!empty($findMobile)) {
                $findMobile->status = self::$STATUS_INACTIVE;
                $findMobile->save();
            }
            return true;
        }
        return false;
    }

    protected function _updateStatus($model, $value)
    {
        $data = $model->where('mobile', $value)->first();
        if (!empty($data)) {
            $data->status = self::$STATUS_INACTIVE;
            $data->save();
            return true;
        }
        return false;
    }

}
