<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Tools\ButtonGenAuto;
use App\Admin\Extensions\Tools\GenAuto;
use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Illuminate\Routing\Router;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Encore\Admin\Controllers\PermissionController as Controller;

class PermissionController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header(trans('admin.permissions'));
            $content->description(trans('admin.list'));
            $content->body($this->grid()->render());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     *
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header(trans('admin.permissions'));
            $content->description(trans('admin.edit'));
            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header(trans('admin.permissions'));
            $content->description(trans('admin.create'));
            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Permission::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->slug(trans('admin.slug'));
            $grid->name(trans('admin.name'));

            $grid->http_path(trans('admin.route'))->display(function ($path) {
                return collect(explode("\r\n", $path))->map(function ($path) {
                    $method = $this->http_method ?: ['ANY'];

                    if (Str::contains($path, ':')) {
                        list($method, $path) = explode(':', $path);
                        $method = explode(',', $method);
                    }

                    $method = collect($method)->map(function ($name) {
                        return strtoupper($name);
                    })->map(function ($name) {
                        return "<span class='label label-primary'>{$name}</span>";
                    })->implode('&nbsp;');

                    $path = '/' . trim(config('admin.route.prefix'), '/') . $path;

                    return "<div style='margin-bottom: 5px;'>$method<code>$path</code></div>";
                })->implode('');
            });
            $grid->tools(function ($tools) {
                $tools->append(new ButtonGenAuto());
            });
            $grid->disableRowSelector();
            $grid->created_at(trans('admin.created_at'));
            $grid->updated_at(trans('admin.updated_at'));


        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        return Admin::form(Permission::class, function (Form $form) {
            $form->display('id', 'ID');

            $form->text('slug', trans('admin.slug'))->rules('required');
            $form->text('name', trans('admin.name'))->rules('required');

            $form->multipleSelect('http_method', trans('admin.http.method'))
                ->options($this->getHttpMethodsOptions())
                ->help(trans('admin.all_methods_if_empty'));
            $form->textarea('http_path', trans('admin.http.path'));

            $form->display('created_at', trans('admin.created_at'));
            $form->display('updated_at', trans('admin.updated_at'));
        });
    }

    /**
     * Get options of HTTP methods select field.
     *
     * @return array
     */
    protected function getHttpMethodsOptions()
    {
        return array_combine(Permission::$httpMethods, Permission::$httpMethods);
    }

    public function autoGenerate(Router $route, Permission $permission)
    {
        $routes = $route->getRoutes()->getRoutesByName();

        $array = array();
        foreach ($routes as $key => $val) {
            $pattern_admin = '/admin/';
            preg_match($pattern_admin, $val->uri, $matches);
            if (!empty($matches)) {

                $pattern = '/\{(.+)\}/';
                $http_path = !empty(preg_replace($pattern, "*", preg_replace($pattern_admin, '', $val->uri))) ?
                    preg_replace($pattern, "*", preg_replace($pattern_admin, '', $val->uri)) : $val->uri;
                $array[] = [
                    'name' => !empty($val->action['as']) ? $val->action['as'] : '',
                    'slug' => !empty($val->action['as']) ? $val->action['as'] : '',
                    'http_method' => is_array($val->methods) ? implode(',', $val->methods) : $val->methods,
                    'http_path' => $http_path
                ];
            }
        }
        if (!empty($array)) {
            $this->_checkDataBeforeSave($array, $permission);
            admin_toastr('Tạo quyền thành công');
        }
        return redirect()->route('permissions.index');
    }

    protected function _checkDataBeforeSave($array, $permission)
    {

        foreach ($array as $val) {
            $find = $permission::where('name', $val['name'])->first();
            if (empty($find)) {
                DB::table('admin_permissions')->insert([
                    'name' => $val['name'],
                    'slug' => $val['slug'],
                    'http_method' => $val['http_method'],
                    'http_path' => $val['http_path'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
