<?php
namespace EPMADD;
use PDO;

date_default_timezone_set ('America/Denver');

foreach (array(
  "/.config/credentials.php",
  "{$_SERVER['DOCUMENT_ROOT']}/../.config/credentials.php",
  "{$_SERVER['DOCUMENT_ROOT']}/.config/credentials.php",
  "../.config/credentials.php",
  "../../.config/credentials.php"
  ) as $f)
    if (file_exists($f)) {
        require_once $f;
        break;
    }
if (!defined('DB_HOST')) {
    if (isset($_SERVER['RDS_HOSTNAME'])) {
        // Amazon Elastic Beanstalk
        define('DB_HOST', $_SERVER['RDS_HOSTNAME']);
        define('DB_PORT', $_SERVER['RDS_PORT']);
        define('DB_NAME', $_SERVER['RDS_DB_NAME']);
        define('DB_USERNAME', $_SERVER['RDS_USERNAME']);
        define('DB_PASSWORD', $_SERVER['RDS_PASSWORD']);
    }
    else { // force an error, mostly for PHPUnit
        define('DB_HOST', 'no host');
        define('DB_PORT', 0);
        define('DB_NAME', 'no db');
        define('DB_USERNAME', 'no user');
        define('DB_PASSWORD', 'no password');
    }
}
if (!defined('SESSION_NAME')) // should be in credentials.php
    define('SESSION_NAME', 'EPMADD');

require_once 'DbAccess.php';
require_once 'Page.php';
require_once 'Security.php';
require_once 'Form.php';
require_once 'Error.php';
require_once 'Access.php';

function dump(&$x, $title = null) {
	if (!empty($title))
		echo "<b><font color=blue size=+2>$title</font></b>";
	echo "<pre>";
	var_dump($x);
	echo "</pre>";
}

function htmlspecial($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

?>