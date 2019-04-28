<?php

namespace App\Http\Controllers\Admin\Widgets;

use App\Models\Privileges;

class MenuWidget extends Widget
{
    private static $DIALOG_ID = 1;
    public function showListWidget($name, $urlconfig, $new_dialog_html = "")
    {
        $view = $this->getDataTableView("DataTable_setting");
        $fields = [];
        foreach (Privileges::$menu_fields as $row) {
            if ($row['listable']) {
                $fields[] = $row;
            }
        }
        $view->with("fields", $fields);
        $view->with("name", $name);
        $view->with($urlconfig);
        $view->with('search_widget', '');
        return $view->with('new_dialog', $new_dialog_html)->render();
    }

    public function showEditWidget($urlconfig)
    {
        $view = $this->getView("MenuEdit");
        $view->with($urlconfig);
        $fields = [];
        foreach (Privileges::$menu_fields as $row) {
            if ($row['editable']) {
                $fields[] = $row;
            }
        }
        return $view->with(['inputs' => $fields, 'dialog_id' => self::$DIALOG_ID++])->render();
    }

    public function tranformSetting(&$data, $in)
    {
    }
}
