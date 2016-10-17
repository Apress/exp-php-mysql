<?php
namespace EPMADD;
/*
	Source code from "Expert PHP and MySQL: Application Design and Development"
	by Marc Rochkind (Apress - 2013)

	WARRANTY: THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER "AS IS"
	AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
	THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
	PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
	CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
	EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
	PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
	PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
	LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
	NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

	No technical support is available for any of this source code. In general,
	you must modify and test this code before incorporating it into your programs.

	Warning: Some code contains mistakes or deliberately incorrect coding for the
	purpose of serving as an example for the book. Please read the book carefully
	to determine which code is suitable for reuse in your own applications.

	Copyright 2013 Marc J. Rochkind. All rights reserved. May be copied and used
	under the BSD-type license at http://basepath.com/aup/copyright.htm.
*/
require_once 'lib/common.php';
use PDOException;

class MyPage extends Page {
    
protected function request() {
    $f = new Form();
    $f->start();
    $f->text('userid', 'User ID:', 50, 'User ID');
    $f->text('pw', 'Password:', 50, 'Password', true, true);
    $f->button('pre_action_login', 'Login');
    $f->button('action_forgot', 'Forgot', false);
    $f->end();
    $this->button('Register', null, 'account.php');
    if (isset($_POST['msg']))
        $this->message($_POST['msg']);
}

protected function pre_action_login() {
    $userid = $_POST['userid'];
    $security = new Security();
    if ($security->check_password($userid, $_POST['pw'],
      $expired)) {
        $this->login_phase1($userid);
        if ($expired) {
            $_SESSION['expired'] = true;
            $security->store_verification($userid, 0);
        }
        $this->transfer('loginverify.php',
          array('action_start' => '1'));
    }
    else {
        Sleep(2);
        $this->transfer('login.php',
          array('msg' => 'User ID and/or password are invalid'));
    }
}

protected function pre_action_logout() {
    $this->logout();
    $this->transfer('login.php');
}

protected function action_forgot() {
    $this->hide_request();
    echo <<<EOT
    <p>
    Your user ID and a temporary password will be sent
    <br>
    to the email you provided when you registered.
EOT;
    $f = new Form();
    $f->start();
    $f->text('email', 'Email:', 100, 'user@domain.com');
    $f->button('action_send', 'Send Email');
    $f->end();
}

protected function action_send() {
    $this->hide_request();
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
        $this->message('Invalid or missing email');
    else {
        $stmt = $this->db->query('select userid from user
          where email = :email',
          array('email' => $_POST['email']));
        if ($row = $stmt->fetch()) {
            $tmp = $this->set_temp_password($row['userid']);
            if (is_null($tmp))
                $this->message('Unable to generate password');
            else if ($this->send_email($row['userid'], $tmp))
                return;
            else
                $this->message('Unable to send mail');
        }
        else
            $this->message('Email address not found');
    }
    $this->action_forgot();
}

protected function send_email($userid, $tmp) {
    $subject = 'Your temporary password';
    $msg = "Your User ID is $userid " .
      "and your temporary password is '$tmp'. " .
      "(If you did not request this, please contact the " .
      "system administrator.)";
    if ($_SERVER['HTTP_HOST'] == 'localhost')
        echo <<<EOT
        <p>
        <div style='border:2px solid;padding:10px;width:500px'>
        <p>(localhost -- not sent)
        <p>Subject: $subject
        <p>Msg: $msg
        </div>
EOT;
    else if (!mail($_POST['email'], $subject, $msg))
        return;
    echo <<<EOT
<p>
Your temporary password has been sent. When you receive it,
<br>
use it to login. You'll then be prompted to choose a new password.
<p>
EOT;
    $this->button('Login', null, 'login.php');
    return true;
}
              
private function set_temp_password($userid) {
    $tmp = bin2hex(openssl_random_pseudo_bytes(6));
    $security = new Security();
    if ($security->set_password($userid, $tmp, true))
        return $tmp;
    return null;
}

}

$page = new MyPage('Login', false);
if (isset($_COOKIE['EPMADD']) &&
    !isset($_POST['pre_action_logout']))
    $page->transfer('login.php',
      array('pre_action_logout' => 1));
else
    $page->go();

?>
