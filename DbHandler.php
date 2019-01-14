<?php

class DbHandler
{
    private $con;
 
    public function __construct()
    {
        require_once dirname(__FILE__) . '/DbConnect.php';
 
        $db = new DbConnect();
        $this->con = $db->connect();
    }

    public function insertWallpaper($title, $url, $width, $height, $price, $tag, $uploader_id)
    {
        $sql = "INSERT INTO wallpaper (title, url, width, height, price, tag, uploader_id) VALUES ('$title', '$url', '$width', '$height', '$price', '$tag', '$uploader_id')";
        $stmt = $this->con->prepare($sql);
        if ($stmt->execute())
            return true;
		
        return false;
    }

    public function getAllWallpapers($id, $type, $page)
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

            case 'favorite':
                $sql = "SELECT w.id, w.title, w.url, w.width, w.height, w.total_wow, u.id, u.name 
                FROM ((favorite AS f INNER JOIN wallpaper AS w ON f.wallpaper_id = w.id) 
                LEFT JOIN user AS u ON w.uploader_id = u.id) 
                WHERE f.user_id = $id ORDER BY w.created_at DESC";
                break;
        }

        $start = $page * PAGE_LIMIT;
        $end = $start + PAGE_LIMIT;
        $sql = $sql . " LIMIT " . $start . ", " . $end;
        
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $stmt->bind_result($id, $title, $url, $width, $height, $total_wow, $u_id, $name);

        $wallpapers = array();
 
        while ($stmt->fetch()) {
            $absurl = BASE_URL . WALLPAPER_PATH . $url;
 
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

    public function searchWallpaper($id, $query, $page)
    {
        $sql = "SELECT w.id, w.title, w.url, w.width, w.height, w.total_wow, u.id, u.name 
        FROM wallpaper AS w LEFT JOIN user AS u ON (w.uploader_id = u.id) 
        WHERE w.title LIKE '%$query%' OR w.tag LIKE '%$query%' 
        ORDER BY w.created_at DESC";

        $start = $page * PAGE_LIMIT;
        $end = $start + PAGE_LIMIT;
        $sql = $sql . " LIMIT " . $start . ", " . $end;
        
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $stmt->bind_result($id, $title, $url, $width, $height, $total_wow, $u_id, $name);

        $wallpapers = array();
 
        while ($stmt->fetch()) {
            $absurl = BASE_URL . WALLPAPER_PATH . $url;
 
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