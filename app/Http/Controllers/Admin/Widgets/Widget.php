<?php

namespace App\Http\Controllers\Admin\Widgets;

use App\Models\DataTable;
use App\Http\Controllers\Admin\Controller;
use Illuminate\Support\Facades\View;

class Widget extends Controller
{
	protected function getView($name)
	{
		return View::make("templates.admin.widgets.".$name);
	}

	protected  function getSearchWidget()
	{
		return '';
	}

	protected function getDataTableView($tpl="DataTable")
	{
		$view=$this->getView($tpl);
		$view->with('search_widget',$this->getSearchWidget());
		return $view;
	}
}
