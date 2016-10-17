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

private $err_flds;
    
function request() {
    $this->action('action_show_form');
}

function action_show_form() {
    if (!$this->is_logged_in() && isset($_GET['menu']))
        $this->message('Not logged in.');
    else {
        $data = null;
        if ($this->is_logged_in()) {
            $stmt = $this->db->query('select * from user
              where userid = :userid',
              array('userid' => $this->userid()));
            if (!($data = $stmt->fetch()))
                throw \Exception('User data not found');
        }
        $this->show_form($data);
    }
}

function action_register() {
    $msgs = array();
    $this->err_flds = array();
    if (!$this->is_logged_in())
        if ($_POST['pw1'] != $_POST['pw2']) {
            $msgs[] = 'Passwords do not match';
            $this->err_flds['pw1'] = 1;
            $this->err_flds['pw2'] = 1;
        }
    if (empty($msgs))
        $this->update($_POST);
    else
        $this->message($msgs);
    $this->show_form($_POST);
}

function update($data) {
    // ToDo: password is not stored
    try {
        if ($this->is_logged_in()) {
            $data['userid'] = $this->userid();
            $this->db->update('user', 'userid', array(
              'first', 'last', 'email', 'phone', 'phone_method'),
              $data, $row_count);
            if ($row_count == 0) {
                $this->message('No changes were made', true);
                return false;
            }
        }
        else {
            // null pk forces insert
            $this->db->update('user', null, array('userid',
              'first', 'last', 'email', 'phone', 'phone_method'),
              $data, $row_count);
            if ($row_count != 1) {
                $this->message('Insert failed');
                return false;
            }
        }
    }
    catch (\Exception $e) {
        $this->message($this->translate_error($e));
        return false;
    }
    $this->message('Successful update', true);
    return true;
}

protected function translate_error($e) {
    if (is_a($e, 'PDOException')) {
        switch ($e->getCode()) {
        case '23000':
            if (preg_match("/for key '(.*)'/",
              $e->getMessage(), $m)) {
                $indexes = array(
                  'PRIMARY' =>
                    array ('User ID is already taken', 'userid'),
                  'userid_UNIQUE' =>
                    array ('User ID is already taken', 'userid'),
                  'email_UNIQUE' =>
                    array ('Email is already taken', 'email'),
                  'phone_UNIQUE' =>
                    array ('Phone is already taken', 'phone'));
                if (isset($indexes[$m[1]])) {
                    $this->err_flds = array($indexes[$m[1]][1] => 1);
                    return $indexes[$m[1]][0];
                }
            }
        break;
        case 'CK001':
            if (preg_match('/: 1644 (.*)@(.*)$/',
              $e->getMessage(), $m)) {
                $this->err_flds = array($m[2] => 1);
                return $m[1];
            }
        }
    }
    return $e->getMessage();
}

function show_form($data = null) {
    if (empty($data['phone_method']))
        $data['phone_method'] = 'sms';
    $form = new Form();
    $form->start($data);
    $form->errors($this->err_flds);
    if (!$this->is_logged_in()) {
        $form->text('userid', 'Desired User ID:', 15,
          'UserID');
        $form->text('pw1', 'Password:', 50,
          'Password', true, true);
        $form->password_strength('pw1', '');
        $form->text('pw2', 'Repeat:', 50,
          'Password', true, true);
    }
    $form->text('first', 'First Name:', 25,
      'First Name');
    $form->hspace(3);
    $form->text('last', 'Last Name:', 25,
      'Last Name', false);
    $form->text('email', 'Email:', 75,
      'you@domain.com');
    $form->text('phone', 'Verification Phone:', 25,
      '303-555-1234');
    $form->radio('phone_method', 'SMS (text)', 'sms');
    $form->hspace(5);
    $form->radio('phone_method', 'Voice', 'voice', false);
    $form->button('action_register', $this->is_logged_in() ?
      'Save' : 'Register');
    // Next line for clickjacking example
    //$form->button('action_disable', 'Disable 2FA');
    $form->end();
    $userid = isset($data['userid']) ? $data['userid'] : '';
    echo <<<EOT
    <script>
    $('#pw1').bind('keydown', function() {
        PasswordDidChange('pw1', '$userid');
    });
    </script>
EOT;
}

}

$page = new MyPage('Register', false);
$page->start_session(); // OK if not logged in
$page->go();

?>
