<?php
/**
 * PNGThumb 
 * 
 * @uses Thumb
 * @package 
 * @version 0.0.1
 * @copyright 2014-2015
 * @author Ryan <canbetter@qq.com> 
 * @license MIT {@link http://ryanli.net}
 */

namespace EasyThumb\Genimg;
class PNGThumb extends Thumb{
    public function createFromFile($file){
        return imagecreatefrompng ($file);
    }
    public function save($res,$file){
        imagepng($res,$file);
    }
}
