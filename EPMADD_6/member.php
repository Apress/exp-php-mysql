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
    
//protected function request() {
//	echo '<p>This is a member-page stub.';
//    dump($_COOKIE);
//    dump($_POST);
//}

protected function request() {
    $f = new Form();
    $f->start($_POST);
    $f->text('last', 'Last Name:', 50,
      'Last Name');
    $f->button('action_find', 'Find', false);
    $f->button('action_new', 'New');
    $f->end();
}

protected function action_find() {
    $stmt = $this->db->query('select member_id, last, first from member where last like :pat',
        array('pat' => "{$_POST['last']}%"));
    if ($stmt->rowCount() == 0)
        $this->message('No records found', true);
    else {
        echo '<p>';
        while ($row = $stmt->fetch()) {
            $name = "{$row['last']}, {$row['first']}";
            echo '<p class=find-choice>';
            $this->button('Detail', array('action_detail' => 1, 'member_id' => $row['member_id']), 'member.php');
            echo "<button class=button onclick=\"DeleteConfirm('$name', '{$row['member_id']}', '{$_SESSION['csrftoken']}');\">Delete</button>";
            echo "&nbsp;&nbsp;$name";
        }
    }
}

protected function action_new() {
    $this->show_form(null);
}

protected function action_detail($pk = null) {
    if (is_null($pk))
        $pk = $_POST['member_id'];
    $stmt = $this->db->query('select * from member left join specialty
  using (specialty_id) where member_id = :member_id',
      array('member_id' => $pk));
    if ($stmt->rowCount() == 0)
        $this->message('Failed to retrieve record.');
    $row = $stmt->fetch();
    $this->show_form($row);
}


protected function action_delete() {
    $stmt = $this->db->query('delete from member where
      member_id = :member_id',
      array('member_id' => $_REQUEST['pk']));
    if ($stmt->rowCount() == 1)
        $this->message('Deleted OK', true);
    else
        $this->message('Nothing deleted');
}

protected function action_save() {
    try {
        if (empty($_POST['specialty_id'])) // form supplies ''
            unset($_POST['specialty_id']);
        if (empty($_POST['premium']))
            $_POST['premium'] = 0;
        $pk = $this->db->update('member', 'member_id',
          array('last', 'first', 'street',
          'city', 'state', 'specialty_id',
          'billing', 'premium', 'contact', 'since'), $_POST);
    }
    catch (\Exception $e) {
$this->show_form($_POST);
throw $e;
        $exc = $e;
        $pk = null;
    }
    $this->action_detail($pk);
    if (isset($exc))
        throw $exc;
    $this->message('Saved OK', true);
}

protected function show_form($row) {
    $f = new Form();
    $f->start($row);
    $f->hidden('member_id', $row['member_id']);
    $f->text('last', 'Last Name:', 30, 'Last Name');
    $f->text('first', 'First:', 20, 'First Name', false);
    $f->text('street', 'Street:', 50, 'Street');
    $f->text('city', 'City:', 20, 'City');
    $f->text('state', 'State:', 10, 'State', false);
    $f->foreign_key('specialty_id', 'name', 'Specialty');
    $f->radio('billing', 'Monthly', 'month');
    $f->hspace(2);
    $f->radio('billing', 'Yearly', 'year', false);
    $f->hspace(2);
    $f->radio('billing', 'Recurring', 'recurring', false);
    $f->menu('contact', 'Contact:',
      array('phone', 'email', 'mail', 'none'), true, 'email');
    $f->checkbox('premium', 'Premium:', false);
    $f->date('since', 'Member Since:', false);
    if ($this->ac->has_permission('member-edit'))
        $f->button('action_save', 'Save');
    $f->end();
}

}

$page = new MyPage('Member', true,
  array('member-edit', 'member-view'));
$page->go();

?>
