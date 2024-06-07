<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BaseReportViewExcel implements FromView, ShouldAutoSize
{
    public $columns;
    public $collection;
    public $viewName;

    public function __construct($columns, $collection, $viewName)
    {
        $this->columns = $columns;
        $this->collection = $collection;
        $this->viewName = $viewName;
    }

    public function view(): View
    {
        return view($this->viewName, [
            'data'     => $this->collection,
            'headings' => $this->columns,
        ]);
    }
}
