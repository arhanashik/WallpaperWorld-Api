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

    public function signup($name, $username, $password, $email, $avatar, $auth_type) {
        $enc_password = md5($password);

        $sql = "INSERT INTO `user` (name, username, password, email, avatar, auth_type) 
        VALUES (" . '"' . $name . '"' . ", '$username', '$enc_password', '$email', '$avatar', '$auth_type')";
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $stmt->close();
		
        return $this->getUser($username, $password, $email);
    }

    public function getUser($username, $password, $email) {
        $enc_password = md5($password);

        $query = "SELECT * FROM `user` WHERE (username='$username' OR email='$email') AND password='$enc_password'";
        if ($result = $this->con->query($query)) {
            $user = array();
            while ($row = $result->fetch_row()) {
                $user['id'] = $row[0];
                $user['name'] = $row[1];
                $user['username'] = $row[2];
                $user['email'] = $row[4];
                $user['upload_count'] = $row[5];
                $user['avatar'] = $row[6];
                $user['auth_type'] = $row[7];
                break;
            }
            $result->close();
            return $user;
        }

        return false;
    }

    public function insertWallpaper($title, $url, $width, $height, $tag, $price, $uploader_id)
    {
        $sql = "INSERT INTO `wallpaper` (title, url, width, height, tag, price, uploader_id) 
        VALUES (" . '"' . $title . '"' . ", '$url', '$width', '$height', '$tag', '$price', '$uploader_id')";
        $stmt = $this->con->prepare($sql);
        if ($stmt->execute()) {
			$stmt->close();
            return $this->getWallpaper($url);
        }
		
        return false;
    }

    public function getWallpaper($url)
    {
        $sql = "SELECT w.id, w.title, w.url, w.width, w.height, w.tag, w.price, w.total_wow, w.total_download, w.status, u.id, u.name
        FROM wallpaper AS w LEFT JOIN user AS u ON (w.uploader_id = u.id) 
        WHERE w.url = '$url'";
        
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $stmt->bind_result($id, $title, $url, $width, $height, $tag, $price, $total_wow, $total_download, $status, $u_id, $name);
 
        while ($stmt->fetch()) {
            $absurl = BASE_URL . WALLPAPER_PATH . $url;
 
            $wallpaper = array();
            $wallpaper['id'] = $id;
            $wallpaper['title'] = $title;
            $wallpaper['url'] = $absurl;
            $wallpaper['width'] = $width;
            $wallpaper['height'] = $height;
            $wallpaper['tag'] = $tag;
            $wallpaper['price'] = $price;
            $wallpaper['total_wow'] = $total_wow;
            $wallpaper['total_download'] = $total_download;
            $wallpaper['status'] = $status;
            $wallpaper['uploader_id'] = $u_id;
            $wallpaper['uploader_name'] = $name;

            return $wallpaper;
        }
 
        return false;
    }

    public function getAllWallpapers($id, $type, $page)
    {
        $sql = "SELECT * FROM wallpaper";
        
        switch($type) {
            case 'collection':
                $sql = "SELECT w.id, w.title, w.url, w.width, w.height, w.tag, w.price, w.total_wow, w.total_download, w.status, u.id, u.name 
                FROM wallpaper AS w LEFT JOIN user AS u ON (w.uploader_id = u.id) 
                WHERE w.status = " . PUBLISHED . " ORDER BY w.created_at DESC";
                break;

            case 'top_chart':
                $sql = "SELECT w.id, w.title, w.url, w.width, w.height, w.tag, w.price, w.total_wow, w.total_download, w.status, u.id, u.name  
                FROM wallpaper AS w LEFT JOIN user AS u ON (w.uploader_id = u.id) 
                WHERE w.status = " . PUBLISHED . " ORDER BY w.total_download DESC";
                break;

            case 'popular':
                $sql = "SELECT w.id, w.title, w.url, w.width, w.height, w.tag, w.price, w.total_wow, w.total_download, w.status, u.id, u.name  
                FROM wallpaper AS w LEFT JOIN user AS u ON (w.uploader_id = u.id) 
                WHERE w.status = " . PUBLISHED . " ORDER BY w.total_wow DESC";
                break;

            case 'premium':
                $sql = "SELECT w.id, w.title, w.url, w.width, w.height, w.tag, w.price, w.total_wow, w.total_download, w.status, u.id, u.name  
                FROM wallpaper AS w LEFT JOIN user AS u ON (w.uploader_id = u.id) 
                WHERE w.status = " . PUBLISHED . " AND  w.price > 0 ORDER BY w.created_at DESC";
                break;

            case 'favorite':
                $sql = "SELECT w.id, w.title, w.url, w.width, w.height, w.tag, w.price, w.total_wow, w.total_download, w.status, u.id, u.name 
                FROM ((favorite AS f INNER JOIN wallpaper AS w ON f.wallpaper_id = w.id) 
                LEFT JOIN user AS u ON w.uploader_id = u.id) 
                WHERE w.status = " . PUBLISHED . " AND f.user_id = $id ORDER BY w.created_at DESC";
                break;

            case 'own':
                $sql = "SELECT w.id, w.title, w.url, w.width, w.height, w.tag, w.price, w.total_wow, w.total_download, w.status, u.id, u.name  
                FROM wallpaper AS w LEFT JOIN user AS u ON (w.uploader_id = u.id) 
                WHERE  w.uploader_id = $id ORDER BY w.created_at DESC";
                break;
        }

        $start = $page * PAGE_LIMIT;
        $end = $start + PAGE_LIMIT;
        $sql = $sql . " LIMIT " . $start . ", " . $end;
        
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $stmt->bind_result($id, $title, $url, $width, $height, $tag, $price, $total_wow, $total_download, $status, $u_id, $name);

        $wallpapers = array();
 
        while ($stmt->fetch()) {
            $absurl = BASE_URL . WALLPAPER_PATH . $url;
 
            $wallpaper = array();
            $wallpaper['id'] = $id;
            $wallpaper['title'] = $title;
            $wallpaper['url'] = $absurl;
            $wallpaper['width'] = $width;
            $wallpaper['height'] = $height;
            $wallpaper['tag'] = $tag;
            $wallpaper['price'] = $price;
            $wallpaper['total_wow'] = $total_wow;
            $wallpaper['total_download'] = $total_download;
            $wallpaper['status'] = $status;
            $wallpaper['uploader_id'] = $u_id;
            $wallpaper['uploader_name'] = $name;

            array_push($wallpapers, $wallpaper);
        }
 
        return $wallpapers;
    }

    public function searchWallpaper($id, $query, $page)
    {
        $sql = "SELECT w.id, w.title, w.url, w.width, w.height, w.tag, w.price, w.total_wow, w.total_download, w.status, u.id, u.name
        FROM wallpaper AS w LEFT JOIN user AS u ON (w.uploader_id = u.id) 
        WHERE w.status = " . PUBLISHED . " AND (w.title LIKE '%$query%' OR w.tag LIKE '%$query%')
        ORDER BY w.created_at DESC";

        $start = $page * PAGE_LIMIT;
        $end = $start + PAGE_LIMIT;
        $sql = $sql . " LIMIT " . $start . ", " . $end;
        
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $stmt->bind_result($id, $title, $url, $width, $height, $tag, $price, $total_wow, $total_download, $status, $u_id, $name);

        $wallpapers = array();
 
        while ($stmt->fetch()) {
            $absurl = BASE_URL . WALLPAPER_PATH . $url;
 
            $wallpaper = array();
            $wallpaper['id'] = $id;
            $wallpaper['title'] = $title;
            $wallpaper['url'] = $absurl;
            $wallpaper['width'] = $width;
            $wallpaper['height'] = $height;
            $wallpaper['tag'] = $tag;
            $wallpaper['price'] = $price;
            $wallpaper['total_wow'] = $total_wow;
            $wallpaper['total_download'] = $total_download;
            $wallpaper['status'] = $status;
            $wallpaper['uploader_id'] = $u_id;
            $wallpaper['uploader_name'] = $name;

            array_push($wallpapers, $wallpaper);
        }
 
        return $wallpapers;
    }

    public function insertFavorite($user_id, $wallpaper_id)
    {
        $sql = "UPDATE `wallpaper` SET total_wow = total_wow + 1 WHERE id = '$wallpaper_id'";
        $stmt = $this->con->prepare($sql);
        if ($stmt->execute()) {
            $sql = "INSERT INTO `favorite`(user_id, wallpaper_id) VALUES ('$user_id', '$wallpaper_id')";
            $stmt = $this->con->prepare($sql);

            if ($stmt->execute())
                return true;
        }
		
        return false;
    }

    public function getFavorite($user_id, $wallpaper_id)
    {
        $query = "SELECT id FROM `favorite` WHERE user_id='$user_id' AND wallpaper_id='$wallpaper_id'";
        if ($result = $this->con->query($query))
            if($row = $result->fetch_row())
                return true;

        return false;
    }

}