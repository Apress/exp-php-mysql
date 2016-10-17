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

define('YUBIKEY', false); // define for Yubikey example

class MyPage extends Page {
    
protected function request() {
    $userid = $this->userid(true);
    if (isset($_SESSION['expired']))
        echo '<p>Your password has expired.';
    $f = new Form();
    $f->start();
    $f->text('pw-old', 'Existing Password:',
      50, 'Existing Password', true, true);
    $f->text('pw-new1', 'New Password:',
      50, 'New Password', true, true);
    $f->password_strength('pw-new1', $userid);
    $f->text('pw-new2', 'Repeat:',
      50, 'New Password', true, true);
    if (YUBIKEY)
        $f->text('yubikey', 'YubiKey:',
          50, '', true, true);
    $f->button('action_set', 'Set');
    $f->end();
}

protected function action_set() {
    $userid = $this->userid(true);
    $security = new Security();
    if ($security->check_password($userid, $_POST['pw-old'],
      $expired)) {
        if ($_POST['pw-new1'] == $_POST['pw-new2']) {
            if ($_POST['pw-new1'] == $_POST['pw-old'])
                $this->message('New password must be different');
            else {
                if (YUBIKEY && !$this->set_yubikey())
                    return;
                $this->hide_request();
                $security->set_password($userid,
                  $_POST['pw-new1']);
                unset($_SESSION['expired']);
                $this->message('Password was changed', true);
                $this->button('Login', null, 'login.php');
            }
        }
        else
            $this->message('New and repeated passwords do
              not match');
    }
    else
        $this->message('Invalid existing password');
}

protected function set_yubikey() {
    if (empty($_SESSION['userid'])) {
        $this->message('No User ID');
        return false;
    }
    $y = $_POST['yubikey'];
    if (strlen($y) < 34) {
        $this->message('Invalid YubiKey OTP');
        return false;
    }
    $identity = substr($y, 0, strlen($y) - 32);
    $this->db->update('user', 'userid',
      array('identity'), array('userid' => $_SESSION['userid'],
      'identity' => $identity));
    return true;
}

}

$page = new MyPage('Change Password');
$page->go(true);

?>
