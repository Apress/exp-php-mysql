<?php
namespace EPMADD;
require_once('lib/PasswordHash.php');
require_once('lib/DbAccess.php');

define('VERIFICATION_COOKIE', 'EPMADD_VER');

class Security {
    protected $hasher, $db;
    
function __construct() {
    $this->hasher = new \PasswordHash(8, false);
    $this->db = new DbAccess();
}

protected function hash($pass) {
    $h = $this->hasher->HashPassword($pass);
    if (strlen($h) < 20) {
        log('Failed to process password');
        return null;
    }
    return $h;
}

protected function get_hashes($userid, &$password_hash,
  &$verification_hash, &$expired) {
    $expired = false;
    try {
        $stmt = $this->db->query('select password_hash,
          verification_hash, expiration, extratime
          from user where userid = :userid',
          array('userid' => $userid));
        if ($row = $stmt->fetch()) {
            $t = strtotime($row['expiration']);
            if ($t < time()) {
                $expired = true;
                log('password expired');
            }
            if ($t + $row['extratime'] >= time()) {
                $password_hash = $row['password_hash']; 
                $verification_hash = $row['verification_hash'];
                return true;
            }
            else
                log('past extra time');
        }
        else
            log('userid not found in database');
    }
    catch (\Exception $e) {
        log($e);
    }
    $password_hash = $verification_hash = null;
    return false;
}

function check_password($userid, $pass, &$expired) {
    if ($this->get_hashes($userid, $password_hash,
      $verification_hash, $expired) &&
      $this->hasher->CheckPassword($pass, $password_hash))
        return true;
    $this->store_verification($userid, 0);
    return false;
}

protected function update_verification_hash($userid, $h) {
    if (empty($userid)) die('Security::update_verification_hash -- userid is empty');
    $this->db->update('user', 'userid',
      array('verification_hash'),
      array('userid' => $userid, 'verification_hash' => $h));
}

function set_password($userid, $pass, $temp = false) {
    try {
        if (isset($_SESSION))
            unset($_SESSION['expired']);
        $this->store_verification($userid);
        $h = $this->hash($pass);
        $time = time() + ($temp ? 0 : 3600 * 24 * 365 * 10);
        $extra = $temp ? 1800 : 3600 * 24 * 30;
        $this->db->update('user', 'userid',
          array('password_hash', 'expiration', 'extratime'),
          array('userid' => $userid, 'password_hash' => $h,
          'expiration' => date('Y-m-d H:i:s', $time),
          'extratime' => $extra));
    }
    catch (\Exception $e) {
        log($e);
        return false;
    }
    return true;
}

protected function screwed_down($token) {
    return $token . $_SESSION['browser'];
}

function check_verification($userid) {
    return isset($_COOKIE[VERIFICATION_COOKIE]) &&
      isset($_SESSION['browser']) &&
      $this->get_hashes($userid, $password_hash,
      $verification_hash, $expired) &&
      $this->hasher->CheckPassword(
      $this->screwed_down($_COOKIE[VERIFICATION_COOKIE]),
      $verification_hash);
}

function store_verification($userid, $store = false)
{
    try {
        if ($store) {
            $time = 30;
            $token = bin2hex(openssl_random_pseudo_bytes(16));
            $h = $this->hash($this->screwed_down($token));
        }
        else {
            $time = -1;
            $token = '0';
            $h = '0';
        }
        $this->update_verification_hash($userid, $h);
        $this->set_cookie(VERIFICATION_COOKIE, $token, $time);
    }
    catch (\Exception $e) {
        log($e);
        return false;
    }
    return true;
}

function set_cookie($name, $value, $expires, $path = null,
  $domain = null, $secure = null) {
    if ($path == null)
        $path = '/EPMADD';
    if ($domain == null)
        $domain = $_SERVER['HTTP_HOST'] == 'localhost' ?
          '' : $_SERVER['HTTP_HOST'];
    if ($secure == null)
        $secure = isset($_SERVER['HTTPS']);
    $sec = $secure ? 'true' : 'false';
    echo <<<EOT
    <script>
    setCookie('$name', '$value', $expires, '$path', '$domain',
      $sec);
    </script>
EOT;
    return true;
}

}

?>
