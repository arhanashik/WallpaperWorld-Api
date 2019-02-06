<?php
 
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ww_db');

define('BASE_URL', 'http://192.168.2.28/ww');

define('UPLOAD_PATH', '/uploads/');
define('WALLPAPER_PATH', UPLOAD_PATH . 'wallpaper/');
define('AVATAR_PATH',  UPLOAD_PATH . 'avatar/');

define('PAGE_LIMIT',  5);

define('ALLOWED_EXTENTION',  array("png","jpg","jpeg"));
define('MAX_SIZE',  2000000);