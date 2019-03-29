<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class ToolsController extends AdminController
{
    private $pridb;
    public function __construct()
    {
        parent::__construct();
    }

    public function getNotePath()
    {
      $id = $this->user()->id;
      $path=storage_path()."/note"; 
      if(!file_exists($path)){
        mkdir($path,0755,true);
      }
      return  $path."/".$id.".txt";
    }

    public function getNote(Request $req)
    {
      $content = '暂无内容';
      if(file_exists($this->getNotePath())){
        $content = file_get_contents($this->getNotePath());
      }
      return json_encode(['code'=>1,'note'=>$content]);
    }

    public function postNoteSave(Request $req)
    {
      $note=$req->get("note");
      $path=storage_path()."/note"; 
      file_put_contents($this->getNotePath(),$note);
      return json_encode(['code'=>1]);
    }
}
