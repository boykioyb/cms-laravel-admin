<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExporter extends AbstractExporter
{
    protected $fields = [];
    protected $titles = [];
    protected $fileName;
    protected $sheetName;

    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    public function setSheetName($sheetName)
    {
        $this->sheetName = $sheetName;
        return $this;
    }

    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    public function setTitles($titles)
    {
        $this->titles = $titles;
        return $this;
    }

    public function export()
    {
        Excel::create($this->fileName, function ($excel) {

            $excel->sheet($this->sheetName, function (LaravelExcelWorksheet $sheet) {

                $this->chunk(function ($records) use ($sheet) {

                    $rows = $records->map(function ($item) {

                        $data = array_only(array_dot($item->toArray()), $this->fields);

                        $result = $this->sortByarray($this->fields, $data);
                        return $result;
                    });

                    if (!empty($this->titles)) {
                        $rows->prepend($this->titles);
                    }

                    $sheet->rows($rows);

                });

            });

        })->export('xls');
    }


    /**
     * @author Hoatq 27-06-2018
     * @param array $originalArray mang ban đầu muốn sắp xếp
     * @param array $data data dữ liệu chứ key giống mảng ban đầu như theo field db và value
     * @return array $result trả về kế quả đã được sắp xếp
     */
    public function sortByarray($originalArray, $data)
    {
        if (empty($data)) return null;

        $result = array();
        foreach ($originalArray as $value) {
            $result[$value] = !empty($data[$value]) ? $data[$value] : '';
        }
        return $result;
    }

}