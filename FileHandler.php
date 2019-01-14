<?php

class FileHandler
{
 
    public function __construct()
    {
        require_once dirname(__FILE__) . '/Constants.php';
    }
 
 
    public function uploadWallpaper($file, $extension)
    {
        $name = round(microtime(true) * 1000) . '.' . $extension;
        $filedest = dirname(__FILE__) . WALLPAPER_PATH . $name;
        
        if (move_uploaded_file($file, $filedest))
            return $name;
		
        return false;
    }
 
}