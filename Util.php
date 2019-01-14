<?php

require_once dirname(__FILE__) . '/Util.php';

class Util
{
	public function getFileExtension($file)
	{
		$path_parts = pathinfo($file);
		return $path_parts['extension'];
	}

	public function isNotSupportedImage($image) {

		if (!file_exists($image['tmp_name'])) {
			return "Choose image file to upload";
		}

		$extension = $file_extension = pathinfo($image["name"], PATHINFO_EXTENSION);
		
		if (!in_array($extension, ALLOWED_EXTENTION)) {
			return "Invalid image. Supported format: " . implode(", ", ALLOWED_EXTENTION);
		}

		if ($image["size"] > MAX_SIZE) {
			return "Image size too large. Max size: " . round(MAX_SIZE/(1024*1024), 1) . "MB";
		}

		return false;
	}

	public function getImageDimen($image)
	{
		$dimen = array();
		
		try {
			$image_info = getimagesize($image);
			$dimen['width'] = $image_info[0];
			$dimen['height'] = $image_info[1];
		} catch (Exception $e) {
			$dimen['width'] = 0;
			$dimen['height'] = 0;
		}

		return $dimen;
	}
	
	public function reArrayFiles($files)
	{
		$file_ary = array();
		$file_count = count($files['name']);
		$file_key = array_keys($files);
		
		for($i=0; $i<$file_count; $i++)
		{
			foreach($file_key as $val)
			{
				$file_ary[$i][$val] = $files[$val][$i];
			}
		}
		return $file_ary;
	}
}