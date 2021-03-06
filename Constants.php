<?php
 
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ww_db');

define('BASE_URL', 'http://192.168.2.53/ww');

define('UPLOAD_PATH', '/uploads/');
define('WALLPAPER_PATH', UPLOAD_PATH . 'wallpaper/');
define('AVATAR_PATH',  UPLOAD_PATH . 'avatar/');

define('UNDEFINED', -2);
define('REJECTED', -1);
define('PENDING', 0);
define('PUBLISHED', 1);
define('HIDDEN', 2);

define('PAGE_LIMIT',  20);

define('ALLOWED_EXTENTION',  array("png","jpg","jpeg"));
define('MAX_SIZE',  2000000);