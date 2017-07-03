<?php

namespace App\Http\Controllers\Widgets;

use App\Models\DataTable;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class Widget extends Controller
{
	protected function getView($name)
	{
		return View::make("widgets.".$name);
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
