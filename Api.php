<?php

require_once dirname(__FILE__) . '/FileHandler.php';
require_once dirname(__FILE__) . '/DbHandler.php';
require_once dirname(__FILE__) . '/Util.php';
 
$response = array();
$response['error'] = true;
$response['message'] = 'Required parameters are missing';
 
if (isset($_GET['call'])) {
    switch ($_GET['call']) {
        case 'upload':
 
            if (isset($_POST['title']) && strlen($_POST['title']) > 0 && isset($_POST['uploader_id'])) {
                $util = new Util();
				$upload = new FileHandler();
				$db = new DbHandler();
 
				$file = $_FILES['file']['tmp_name'];

				$invalidImage = $util->isNotSupportedImage($_FILES['file']);
				if($invalidImage){
					$response['message'] = $invalidImage;
				}
				else {
					$title = $_POST['title'];
					$dimen = $util->getImageDimen($file);
					$price = $_POST['price'];
					$tag = $_POST['tag'];
					$uploader_id = $_POST['uploader_id'];

					$url = $upload->uploadWallpaper($file, $util->getFileExtension($_FILES['file']['name']));
					if (!$url) {
						$response['message'] = 'File not uploaded';
					
					} else {
						$inserted = $db->insertWallpaper($title, $url, $dimen['width'], $dimen['height'], $price, $tag, $uploader_id);
						if($inserted) {
							$absurl = BASE_URL . WALLPAPER_PATH . $url;
						
							$response['error'] = false;
							$response['message'] = 'File Uploaded Successfullly';
							$response['url'] = $absurl;
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
				$util = new Util();
				$upload = new FileHandler();
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
				$response['message'] = 'File Uploaded Successfullly';
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

			$db = new DbHandler();
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

			$db = new DbHandler();
			$wallpapers = $db->searchWallpaper($id, $query, $page);

            $response['error'] = false;
            $response['message'] = 'Total ' . count($wallpapers) . ' wallpaper(s) found';
            $response['wallpapers'] = $wallpapers;
 
            break;
    }
}
 
echo json_encode($response);