<?php
namespace EPMADD;
use PDO;

if (!defined('CHAPTER_5'))
    define('CHAPTER_5', false);
class Page {

protected $title, $want_session, $permissions, $db, $incl_dir, $error;

function __construct($title, $want_session = true,
  $permissions = null, $incl_dir = 'incl') {
    $this->title = $title;
    $this->want_session = $want_session;
    $this->permissions = $permissions;
    $this->db = new DbAccess();
    $this->incl_dir = $incl_dir;
    $this->error = new Error();
    $this->ac = new Access($this->db);
}

function getdb() {
    return $this->db;
}

private function array_to_js($a) {
    if (empty($a))
        $x = '{}';
    else {
        $x = '';
        foreach ($a as $k => $v)
            $x .= ",'$k': '$v'";
        $x = '{' . substr($x, 1) . '}';
    }
    return $x;
}

protected function button($label, $params, $path = null,
  $popup = false) {
    if (is_null($path))
        $path = $_SERVER['PHP_SELF'];
    if (strpos($path, '?') !== false)
        die('illegal parameter in button() action');
    $x = $this->array_to_js($params);
    if ($popup)
        echo "<button class=button onclick=\"
          openWindow('$path', $x);\">$label</button>";
    else
        echo "<button class=button onclick=\"
          transfer('$path', $x);\">$label</button>";
}

public function transfer($path, $params = null) {
    if (is_null($path))
        $path = $_SERVER['PHP_SELF'];
    $x = $this->array_to_js($params);
    $this->top();
    echo <<<EOT
    <script>
    transfer('$path', $x);
    </script>
EOT;
}

protected function message($s, $ok = false) {
    if ($ok)
        $id = 'message-ok';
    else
        $id = 'message-error';
    if (gettype($s) == 'array') {
        $m = '';
        foreach ($s as $x)
            $m .= '<br>' . htmspecial($x);
    }
    else {
        $s = str_replace('"', "'", $s);
        $s = str_replace("\r", '', $s);
        $s = str_replace("\n", ' ', $s);
        $m = htmlspecial($s);
    }
    echo <<<EOT
        <script>
        $(document).ready(function () {
            $('#div-message').css('padding', '10px');
            $('#$id').html("$m");
        });
        </script>
EOT;
}

public function go($pending_ok = false) {
    header('X-Frame-Options: deny');
    header('X-XSS-Protection: 0'); // for testing only
    if ($this->want_session)
        $this->start_session();
    try {
        $this->ac->check_permissions($this->permissions);
        if ($this->perform_action(true)) // actions before output
            return;
        $this->top();
    }
    catch (\Exception $e) {
        log($e);
        $this->top();
        echo '<p class=message-error>' .
          $e->getMessage() . '</p>';
        $this->bottom();
        return;
    }
    echo <<<EOT
<div class=div-message id=div-message>
<p class=message-error id=message-error></p>
<p class=message-ok id=message-ok></p>
</div>
EOT;
    if ($this->want_session && !$this->is_logged_in() &&
      (!$pending_ok || !$this->is_logging_in())) {
        $this->message("Not logged in.");
        echo '<div class=div-process></div>'; // to get spacing
        $this->bottom();
        exit();
    }
    try {
        echo '<div id=div-request class=div-request>';
        $this->request();
        echo '</div>';
        echo '<div class=div-process>';
        $this->perform_action();
        echo '</div>';
    }
    catch (\Exception $e) {
        $this->message($this->translate_error($e));
    }
    $this->bottom();
}

protected function translate_error($e) {
    return $e->getMessage();
}

private function perform_action($want_pre = false) {
    if ($want_pre)
        $pfx = 'pre_action_';
    else
        $pfx = 'action_';
    // Temp code to trap any use of GET
    // Not to be used if testing Chapter 5 code
    if (!CHAPTER_5)
        foreach ($_GET as $k => $v)
            if (strpos($k, $pfx) === 0) {
                echo '<pre>';
                echo "Bad action: $k\n";
                print_r(debug_backtrace());
                echo '</pre>';
            }
    foreach ($_REQUEST as $k => $v)
        if (strpos($k, $pfx) === 0) {
            if (!$this->security_check() && !CHAPTER_5)
                throw new \Exception('Invalid form');
            $this->$k();
            return true;
        }
    return false;
}

protected function security_check() {
    if (isset($_SESSION) && (!isset($_POST['csrftoken']) ||
      $_POST['csrftoken'] != $_SESSION['csrftoken']))
        return false;
    // HTTP_REFERER easily spoofed, but make the check anyway
    if (isset($_SERVER['HTTP_REFERER']) &&
      parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) !=
      $_SERVER['HTTP_HOST'])
        return false;
    return true;
}

// called from request only -- performs action only if no other actons
protected function action($x) {
    if (count(preg_grep('/^action_/', array_keys($_POST))) == 0)
        $this->transfer(null, array($x => '1'));
    $this->hide_request();
}

protected function request() {
    $this->hide_request();
}

protected function top() {
    require_once "{$this->incl_dir}/top.php";
}

protected function bottom() {
    require_once "{$this->incl_dir}/bottom.php";
}

protected function hide_request() {
	echo <<<EOT
		<script>
		$(document).ready(function () {
			$('#div-request').hide();
		});
		</script>
EOT;
}

public function start_session() {
    // Next 2 lines from "Pro PHP Security", 2nd Ed.
    // (Snyder Myer, and Southwell), p. 100
    ini_set('session.use_only_cookies', TRUE);
    ini_set('session.use_trans_sid', FALSE);
    session_name(SESSION_NAME);
    session_start();
    if (empty($_SESSION['csrftoken']))
        $_SESSION['csrftoken'] =
          bin2hex(openssl_random_pseudo_bytes(8));
}

// Copied from php.net/manual/en/function.session-destroy.php
private function destroy_session() {
	$_SESSION = array();
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
		  $params["path"], $params["domain"],
		  $params["secure"], $params["httponly"]);
	}
	session_destroy();
}

protected function is_logged_in() {
    return !empty($_SESSION['userid']);
}

protected function is_logging_in() {
    return !empty($_SESSION['userid_pending']);
}

protected function login($login) {
    $this->start_session();
    $_SESSION['userid'] = $login;
}

protected function login_phase1($login) {
    $this->start_session();
    unset($_SESSION['verification_code']);
    unset($_SESSION['userid']);
    $_SESSION['userid_pending'] = $login;
    return true;
}

protected function login_phase2() {
    $_SESSION['userid'] = $_SESSION['userid_pending'];
    unset($_SESSION['userid_pending']);
    $this->ac->load_permissions();
}

protected function logout() { // must be called before output
    $this->start_session();
    $this->destroy_session();
}

protected function userid($pendingOK = false) {
    $userid = empty($_SESSION['userid']) ? null :
      $_SESSION['userid'];
    if (is_null($userid) && $pendingOK)
        $userid = empty($_SESSION['userid_pending']) ? null :
          $_SESSION['userid_pending'];
    return $userid;
}

}
?>
