<?php
namespace EPMADD;
use PDO;

class Page {

protected $title, $want_session, $db, $incl_dir;

function __construct($title, $want_session = true, $incl_dir = 'incl') {
    $this->title = $title;
    $this->want_session = $want_session;
    $this->db = new DbAccess();
    $this->incl_dir = $incl_dir;
}

protected function button($label, $params, $action = null, $target = '_self') {
	static $button_number;

    $event = '';
	$button_number++;
	if ($target == '_popup') {
		$target = "Window_$button_number_" . (int)(100000 * microtime(true));
		$attributes = 'height=800, width=1200, top=100, left=100, tab=no, location=no, menubar=no, status=no, toolbar=no';
		$event = "onclick=\"window.open('', '$target', '$attributes');\"";
	}
	if (empty($action))
		$action = $_SERVER['PHP_SELF'];
	echo " <form action='$action' method=post accept-charset=UTF-8 target='$target' style='display:inline;'>";
	foreach ($params as $param => $value)
		echo "<input type=hidden name='$param' value='$value'>";
	echo "<input class=button type=submit value='$label' $event></form>";
}

protected function message($s, $ok = false) {
    if ($ok)
        $id = 'message-ok';
    else
        $id = 'message-error';
    $s = str_replace('"', "'", $s);
    $s = str_replace("\r", '', $s);
    $s = str_replace("\n", ' ', $s);
    $s = htmspecial($s);
    echo <<<EOT
        <script>
        $(document).ready(function () {
            $('#div-message').css('padding', '10px');
            $('#$id').html("$s");
        });
        </script>
EOT;
}

public function go() {
    if ($this->want_session)
        $this->start_session();
    try {
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
    if ($this->want_session && !$this->is_logged_in()) {
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
        $this->message($e->getMessage());
    }
    $this->bottom();
}

private function perform_action($want_pre = false) {
    if ($want_pre)
        $pfx = 'pre_action_';
    else
        $pfx = 'action_';
    foreach ($_REQUEST as $k => $v)
        if (strpos($k, $pfx) === 0) {
            $this->$k();
            return true;
        }
    return false;
}

protected function request() {
}

protected function top() {
    require_once "{$this->incl_dir}/top.php";
}

protected function bottom() {
    require_once "{$this->incl_dir}/bottom.php";
}

private function start_session() {
    // Next 2 lines from "Pro PHP Security", 2nd Ed.
    // (Snyder Myer, and Southwell), p. 100
    ini_set('session.use_only_cookies', TRUE);
    ini_set('session.use_trans_sid', FALSE);
    session_name(SESSION_NAME);
    session_start();
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

protected function login($login) {
    $this->start_session();
    $_SESSION['userid'] = $login;
}

protected function logout() { // must be called before output
    $this->start_session();
    $this->destroy_session();
}

}
?>
