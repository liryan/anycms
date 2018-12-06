<?php

namespace App\Http\Controllers\Admin\Widgets;

use App\Http\Controllers\Admin\Controller;
use Illuminate\Support\Facades\View;

/**
 * Undocumented class
 */
class Widget extends Controller
{
    protected function getView($name)
    {
        return View::make("templates.admin.widgets." . $name);
    }

    protected function getSearchWidget()
    {
        return '';
    }
    /**
     * a simple method
     *
     * @param string $tpl
     * @return void
     */
    protected function getDataTableView($tpl = "DataTable")
    {
        $view = $this->getView($tpl);
        $view->with('search_widget', $this->getSearchWidget());
        return $view;
    }
}
