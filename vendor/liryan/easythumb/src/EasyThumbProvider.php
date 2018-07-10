<?php
/**
 * EasyThumbProvider 
 * 
 * @uses ServiceProvider
 * @package 
 * @version 0.0.1
 * @copyright 2014-2015
 * @author Ryan <canbetter@qq.com> 
 * @license MIT {@link http://ryanli.net}
 */

namespace EasyThumb;
use \Illuminate\Support\ServiceProvider;
class EasyThumbProvider extends ServiceProvider{
    public function register()
    {
        $this->app->singleton('easythumb', function () {
             return new EasyFile();
        });
    }
}
