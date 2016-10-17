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
require_once 'lib/sendcode.php';
use PDOException;

define('TESTING', true); // set to false for real 2FA

define('YUBIKEY', false); // define for Yubikey example

// Following assumes Ybikey API is installed
if (YUBIKEY)
    require_once 'lib/php-yubico-master/Yubico.php';

class MyPage extends Page {

protected function action_start() {
    echo <<<EOT
    <script>
    browser_signature('loginverify.php',
      {'action_start2': '1'});
    </script>
EOT;
}

protected function action_start2() {
    $_SESSION['browser'] = $_POST['browser'];
    log($_SESSION['browser']);
    $security = new Security();
    if ($security->
      check_verification($_SESSION['userid_pending']))
        $this->is_verified();
    else if (YUBIKEY)
        $this->show_form_yubikey();
    else
        $this->show_form_sendcode();
}

protected function action_sendcode() {
    $stmt = $this->db->query('select phone, phone_method from
      user where userid = :userid',
      array('userid' => $_SESSION['userid_pending']));
    if ($row = $stmt->fetch()) {
        $_SESSION['verification_code'] = mt_rand(100000, 999999);
        $error = null;
        if (TESTING || SendCode($row['phone'],
          $_SESSION['verification_code'],
          $row['phone_method'] == 'sms', $error))
            $this->show_form_checkcode();
        else
            $this->message($error);
    }
    else
        $this->message('Failed to retrieve user data');
}

protected function action_checkcode() {
    if (isset($_SESSION['verification_code']) &&
      (TESTING || $_POST['code'] == $_SESSION['verification_code'])) {
        unset($_SESSION['verification_code']);
        if (!isset($_SESSION['expired'])) {
            $security = new Security();
            $security->store_verification($_SESSION['userid_pending'], true);
        }
        $this->is_verified();
    }
    else {
        $this->show_form_checkcode();
        $this->message('Invalid code');
    }
}

protected function is_verified() {
    if (isset($_SESSION['expired']))
        $this->transfer('chgpassword.php');
    else {
        $this->login_phase2();
        $this->transfer('member.php');
    }
}

protected function show_form_sendcode() {
    echo <<<EOT
    <p>Click the button to receive your verification code
    <br>by phone so your login can be completed.
    <p>
EOT;
    $this->button('Send Code', array('action_sendcode' => '1'),
      'loginverify.php');
    echo '<p>';
}

protected function show_form_checkcode() {
    echo <<<EOT
<p>
The phone you specified for verification has been called.
<br>
Please enter the 6-digit code you receive below.
<p>
EOT;
    $f = new Form();
    $f->start();
    $f->text('code', 'Verification Code:', 20, '6-digit code');
    $f->button('action_checkcode', 'Verify', false);
    $f->end();
}

protected function show_form_yubikey() {
    echo <<<EOT
<p>
Position the input cursor in the field and
touch the Yubikey button for one second.
<br>
Then click the Verify button.
<p>
EOT;
    $f = new Form();
    $f->start();
    $f->text('yubikey', 'YubiKey:', 50, '', true, true);
    $f->button('action_yubikey', 'Verify', false);
    $f->end();
}

protected function action_yubikey() {
    $y = $_POST['yubikey'];
    if (strlen($y) > 34) {
        $identity = substr($y, 0, strlen($y) - 32);
        $stmt = $this->db->query('select identity from
          user where userid = :userid',
          array('userid' => $_SESSION['userid_pending']));
        if (($row = $stmt->fetch()) &&
          $row['identity'] == $identity) {
            $yubi = new \Auth_Yubico(CLIENT_ID, CLIENT_KEY);
            if ($yubi->verify($y) === true) {
                if (!isset($_SESSION['expired'])) {
                    $security = new Security();
                    $security->store_verification(
                      $_SESSION['userid_pending'], true);
                }
                $this->is_verified();
                return;
            }
        }
    }
    $this->show_form_yubikey();
    $this->message('Invalid YubiKey OTP');
}


}

$page = new MyPage('Login');
$page->go(true);

?>
