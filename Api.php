<?php

require_once dirname(__FILE__) . '/FileHandler.php';
require_once dirname(__FILE__) . '/DbHandler.php';
require_once dirname(__FILE__) . '/Util.php';
 
$response = array();
$response['error'] = true;
$response['message'] = 'Required parameter(s) are missing';

$util = new Util();
$upload = new FileHandler();
$db = new DbHandler();
 
if (isset($_GET['call'])) {
    switch ($_GET['call']) {
		case 'signup':
			if (isset($_POST['name']) && strlen($_POST['name']) > 0 && (isset($_POST['username']) || isset($_POST['email']))
				&& isset($_POST['password']) && isset($_POST['avatar']) && isset($_POST['auth_type']) ) {

				$name = $_POST['name'];
				$username = $_POST['username'];
				$password = $_POST['password'];
				$email = $_POST['email'];
				$avatar = $_POST['avatar'];
				$auth_type = $_POST['auth_type'];
				
				if(!empty($name) && !(empty($username) || empty($email)) && !empty($password) && !empty($avatar) && !empty($auth_type)) {
					if($user = $db->getUser($username, $password, $email)) {
						$response['error'] = false;
						$response['message'] = 'Welcome back ' . $name;
						$response['user'] = $user;
					} else {
						if($user = $db->signup($name, $username, $password, $email, $avatar, $auth_type)) {
							$response['error'] = false;
							$response['message'] = 'Signup successfull';
							$response['user'] = $user;
						}
					}
				}
			}
			break;
        case 'upload':
 
            if (isset($_POST['title']) && strlen($_POST['title']) > 0 && isset($_POST['uploader_id'])) {
				$file = $_FILES['file']['tmp_name'];

				$invalidImage = $util->isNotSupportedImage($_FILES['file']);
				if($invalidImage){
					$response['message'] = $invalidImage;
				}
				else {
					$title = $_POST['title'];
					$dimen = $util->getImageDimen($file);
					$tag = $_POST['tag'];
					$price = $_POST['price'];
					$uploader_id = $_POST['uploader_id'];

					$url = $upload->uploadWallpaper($file, $util->getFileExtension($_FILES['file']['name']));
					if (!$url) {
						$response['message'] = 'File not uploaded';
					} else {
						$insertedWallpaper = $db->insertWallpaper($title, $url, $dimen['width'], $dimen['height'], $tag, $price, $uploader_id);
						if($insertedWallpaper) {
							$absurl = BASE_URL . WALLPAPER_PATH . $url;
						
							$response['error'] = false;
							$response['message'] = 'File Uploaded Successfully';
							$response['wallpaper'] = $insertedWallpaper;
						}else {
							$response['message'] = 'File uploaded but not added to db';
						}
					}
				}
            }
 
            break;
			
		case 'multiple_upload':

			$files_arr = $_FILES['files'];
			if (!empty($files_arr)) {
				$urls = array();
				
				$files_desc = $util->reArrayFiles($files_arr);
				
				foreach ($files_desc as $file_desc) {
					$file = $file_desc['tmp_name'];
					$desc = "test multi file upload";
					
					$file_name = $upload->saveFile($file, $util->getFileExtension($file_desc['name']), $desc);
					if (strlen($file_name) > 0) {
						$absurl = 'http://' . gethostbyname(gethostname()) . '/FileUploadApi' . UPLOAD_PATH . $file_name;
						array_push($urls, $absurl);
					}
				}
				
				$response['error'] = false;
				$response['message'] = 'File Uploaded Successfully';
				$response['urls'] = $urls;

			}

			break;
 
		case 'wallpapers':
		
			$id = -1;
			if(!empty($_GET['id'])) $id = $_GET['id'];
 
			$type = 'collection';
			if(!empty($_GET['type'])) $type = $_GET['type'];

			$page = 0;
			if(!empty($_GET['page'])) $page = $_GET['page'];

			$wallpapers = $db->getAllWallpapers($id, $type, $page);

            $response['error'] = false;
            $response['message'] = 'Total ' . count($wallpapers) . ' wallpaper(s) found';
            $response['wallpapers'] = $wallpapers;
 
			break;
			
		case 'search':
		
			$id = -1;
			if(!empty($_GET['id'])) $id = $_GET['id'];
 
			$query = '';
			if(!empty($_GET['query'])) $query = $_GET['query'];

			$page = 0;
			if(!empty($_GET['page'])) $page = $_GET['page'];

			$wallpapers = $db->searchWallpaper($id, $query, $page);

            $response['error'] = false;
            $response['message'] = 'Total ' . count($wallpapers) . ' wallpaper(s) found';
            $response['wallpapers'] = $wallpapers;
 
			break;
			
		case 'favorite':

			if(!empty($_POST['id']) && !empty($_POST['wallpaper_id'])) {
				$user_id =  $_POST['id'];
				$wallpaper_id = $_POST['wallpaper_id'];

				if($db->getFavorite($user_id, $wallpaper_id)) {
					$response['message'] = 'Already added to your favorite list!';
					
				} else {
					if($db->insertFavorite($user_id, $wallpaper_id)) {
						$response['error'] = false;
						$response['message'] = 'Wallpaper added to favorite list!';
					}else {
						$response['message'] = 'Failed to add in favorite list!';
					}
				}
			}
 
			break;
			
		case 'remove_favorite':

			if(!empty($_POST['id']) && !empty($_POST['wallpaper_id'])) {
				$user_id =  $_POST['id'];
				$wallpaper_id = $_POST['wallpaper_id'];

				if(!$db->getFavorite($user_id, $wallpaper_id)) {
					$response['message'] = 'This item is removed from your favorite list!';
					
				} else {
					if($db->removeFavorite($user_id, $wallpaper_id)) {
						$response['error'] = false;
						$response['message'] = 'Wallpaper removed from favorite list!';
					}else {
						$response['message'] = 'Failed to removed from  favorite list!';
					}
				}
			}
 
            break;
    }
}
 
echo json_encode($response);