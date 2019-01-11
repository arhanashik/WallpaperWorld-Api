<?php

class FileHandler
{
 
    private $con;
 
    public function __construct()
    {
        require_once dirname(__FILE__) . '/DbConnect.php';
 
        $db = new DbConnect();
        $this->con = $db->connect();
    }
 
 
    public function saveWallpaper($file, $extension, $title, $width, $height, $price, $uploader_id)
    {
        $name = round(microtime(true) * 1000) . '.' . $extension;
        $filedest = dirname(__FILE__) . WALLPAPER_PATH . $name;
        move_uploaded_file($file, $filedest);
 
        $sql = "INSERT INTO wallpaper (title, url, width, height, price, uploader_id) VALUES ('$title', '$name', '$width', '$height', '$price', '$uploader_id')";
        $stmt = $this->con->prepare($sql);
        if ($stmt->execute())
            return $name;
		
        return false;
    }
 
    public function getAllWallpapers($type)
    {
        $sql = "SELECT * FROM wallpaper";
        
        switch($type) {
            case 'collection':
                $sql = "SELECT w.id, w.title, w.url, w.width, w.height, w.total_wow, u.id, u.name FROM wallpaper AS w LEFT JOIN user AS u ON (w.uploader_id = u.id) ORDER BY w.created_at DESC";
                break;

            case 'top_chart':
                $sql = "SELECT w.id, w.title, w.url, w.width, w.height, w.total_wow, u.id, u.name FROM wallpaper AS w LEFT JOIN user AS u ON (w.uploader_id = u.id) ORDER BY w.total_download DESC";
                break;

            case 'popular':
                $sql = "SELECT w.id, w.title, w.url, w.width, w.height, w.total_wow, u.id, u.name FROM wallpaper AS w LEFT JOIN user AS u ON (w.uploader_id = u.id) ORDER BY w.total_wow DESC";
                break;

            case 'premium':
                $sql = "SELECT w.id, w.title, w.url, w.width, w.height, w.total_wow, u.id, u.name FROM wallpaper AS w LEFT JOIN user AS u ON (w.uploader_id = u.id) WHERE w.price > 0 ORDER BY w.created_at DESC";
                break;

            case 'favourite':
                $sql = "SELECT w.id, w.title, w.url, w.width, w.height, w.total_wow, u.id, u.name FROM wallpaper AS w LEFT JOIN user AS u ON (w.uploader_id = u.id) ORDER BY w.created_at DESC";
                break;
        }
        
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $stmt->bind_result($id, $title, $url, $width, $height, $total_wow, $u_id, $name);
        $result = $this->con->query($sql);

        $wallpapers = array();
 
        while ($stmt->fetch()) {
            $absurl = 'http://' . gethostbyname(gethostname()) . '/ww' . WALLPAPER_PATH . $url;
 
            $wallpaper = array();
            $wallpaper['id'] = $id;
            $wallpaper['title'] = $title;
            $wallpaper['url'] = $absurl;
            $wallpaper['width'] = $width;
            $wallpaper['height'] = $height;
            $wallpaper['total_wow'] = $total_wow;
            $wallpaper['uploader_id'] = $u_id;
            $wallpaper['uploader_name'] = $name;

            array_push($wallpapers, $wallpaper);
        }
 
        return $wallpapers;
    }
 
}