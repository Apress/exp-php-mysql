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
set_include_path('../EPMADD_6:../EPMADD_7');
require_once 'lib/common.php';
use PDOException;

class MyPage extends Page {

private $err_flds;

protected function request() {
    $f = new Form();
    $f->start($_POST);
    $f->text('last', 'Lsst Name:', 30,'Last Name');
    $f->button('action_find', 'Find', false);
    $f->button('action_new', 'New');
    $f->end();
}

protected function action_find() {
    $stmt = $this->db->query('select userid, last, first from user
      where last like :pat',
        array('pat' => "{$_POST['last']}%"));
    if ($stmt->rowCount() == 0)
        $this->message('No records found', true);
    else {
        echo '<p>';
        while ($row = $stmt->fetch()) {
            $name = "{$row['last']}, {$row['first']}";
            echo '<p class=find-choice>';
            $this->button('Detail', array('action_detail' => 1,
              'userid' => $row['userid']), 'user.php');
            echo "<button class=button onclick=\"DeleteConfirm('$name',
              '{$row['userid']}', '{$_SESSION['csrftoken']}');\">Delete</button>";
            echo "&nbsp;&nbsp;$name";
        }
    }
}

protected function action_new() {
    $this->show_form(null);
}

protected function action_detail($pk = null) {
    if (is_null($pk))
        $pk = $_POST['userid'];
    $stmt = $this->db->query('select * from user
      where userid = :userid',
      array('userid' => $pk));
    if ($stmt->rowCount() == 0)
        $this->message('Failed to retrieve record.');
    $row = $stmt->fetch();
    $this->show_form($row);
}


protected function action_delete() {
    $stmt = $this->db->query('delete from user where
      userid = :userid',
      array('userid' => $_REQUEST['pk']));
    if ($stmt->rowCount() == 1)
        $this->message('Deleted OK', true);
    else
        $this->message('Nothing deleted');
}

protected function action_save() {
    try {
        $db = $this->db->getPDO();
        $db->beginTransaction();
        $fields = array('first', 'last', 'email', 'phone', 'phone_method');
        if ($_POST['new'] === '1') {
            $pkfield = null;
            $fields[] = 'userid';
        }
        else
            $pkfield = 'userid';
        $this->db->update('user', $pkfield, $fields, $_POST);
        $this->db->query('delete from user_role where
          userid = :userid',
          array('userid' => $_POST['userid']));
        if (isset($_POST['role']))
            foreach ($_POST['role'] as $p)
                $this->db->query('insert into user_role
                  set userid = :userid, role = :role',
                  array('userid' => $_POST['userid'],
                  'role' => $p));
        $db->commit();
    }
    catch (\Exception $e) {
        $db->rollback();
        $this->show_form($_POST);
        throw $e;
    }
    $this->action_detail($_POST['userid']);
    if (isset($exc))
        throw $exc;
    $this->message('Saved OK', true);
}

function show_form($data = null) {
    $new = is_null($data);
    if (empty($data['phone_method']))
        $data['phone_method'] = 'sms';
    $f = new Form();
    $f->start($data);
    $f->errors($this->err_flds);
    $f->hidden('new', $new ? '1' : '0');
    if ($new) {
        $readonly = false;
        $userid = '';
    }
    else {
        $readonly = true;
        $userid = $data['userid'];
    }
    $f->text('userid', 'User ID:', 25,
      'User ID', true, false, $readonly);
    $f->text('first', 'First Name:', 25,
      'First Name');
    $f->hspace(3);
    $f->text('last', 'Last Name:', 25,
      'Last Name', false);
    $f->text('email', 'Email:', 75,
      'you@domain.com');
    $f->text('phone', 'Verification Phone:', 25,
      '303-555-1234');
    $f->radio('phone_method', 'SMS (text)', 'sms');
    $f->hspace(5);
    $f->radio('phone_method', 'Voice', 'voice', false);
    echo '<p class=label>Roles:';
    if ($new && false)
        $stmt = $this->db->query('select * from role order by role');
    else
        $stmt = $this->db->query('select * from role
          left join (select * from user_role where userid = :userid) as ur using (role)
          order by role',
          array('userid' => $userid));
    for ($n = 1; $row = $stmt->fetch(); $n++) {
        echo '<br>';
        $fld = "fld_$n";
        $checked = isset($row['userid']) ? 'checked' : '';
        echo "<input id=$fld type=checkbox name=role[]
          value={$row['role']} $checked>";
        $f->label($fld, $row['role'], false);
    }
    $f->button('action_save', 'Save');
    $f->end();
    $this->ac->show_permissions($userid);
}

}

$page = new MyPage('User', true, 'admin');
$page->go();

?>
