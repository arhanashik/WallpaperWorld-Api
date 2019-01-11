<?php

require_once dirname(__FILE__) . '/FileHandler.php';
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
 
                $file = $_FILES['file']['tmp_name'];
                $title = $_POST['title'];
                $price = $_POST['price'];
                $uploader_id = $_POST['uploader_id'];
				
				$file_name = $upload->saveWallpaper($file, $util->getFileExtension($_FILES['file']['name']), $title, 0, 0, $price, $uploader_id);
                if (!$file_name) {
					$response['error'] = true;
					$response['message'] = 'File uploaded but database entry failed.';
				
                } else {
					$absurl = 'http://' . gethostbyname(gethostname()) . '/ww' . WALLPAPER_PATH . $file_name;
					
                    $response['error'] = false;
                    $response['message'] = 'File Uploaded Successfullly';
                    $response['url'] = $absurl;
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
 
			$type = 'collection';
			if(!empty($_GET['type'])) $type = $_GET['type'];

			$handler = new FileHandler();
			$wallpapers = $handler->getAllWallpapers($type);

            $response['error'] = false;
            $response['message'] = 'Total ' . count($wallpapers) . ' wallpaper(s) found';
            $response['wallpapers'] = $wallpapers;
 
            break;
    }
}
 
echo json_encode($response);