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
    
protected function request() {
    $f = new Form();
    $f->start($_POST);
    $f->text('permission', 'Permission:', 30, 'permission');
    $f->button('action_find', 'Find', false);
    $f->button('action_new', 'New');
    $f->end();
}

protected function action_find() {
    $stmt = $this->db->query('select permission from permission
      where permission like :pat',
      array('pat' => "{$_POST['permission']}%"));
    if ($stmt->rowCount() == 0)
        $this->message('No records found', true);
    else {
        echo '<p>';
        while ($row = $stmt->fetch()) {
            echo '<p class=find-choice>';
            $this->button('Detail', array('action_detail' => 1,
              'permission' => $row['permission']), 'permission.php');
            echo "<button class=button onclick=\"DeleteConfirm('{$row['permission']}',
              '{$row['permission']}', '{$_SESSION['csrftoken']}');\">Delete</button>";
            echo "&nbsp;&nbsp;{$row['permission']}";
        }
    }
}

protected function action_new() {
    $this->show_form(null);
}

protected function action_detail($pk = null) {
    if (is_null($pk))
        $pk = $_POST['permission'];
    // check that it exists
    $stmt = $this->db->query('select permission from permission
      where permission = :permission',
      array('permission' => $pk));
    if ($stmt->rowCount() == 0)
        $this->message('Failed to retrieve record.');
    $row = $stmt->fetch();
    $this->show_form($row);
}


protected function action_delete() {
    $stmt = $this->db->query('delete from permission where
      permission = :permission',
      array('permission' => $_REQUEST['pk']));
    if ($stmt->rowCount() == 1)
        $this->message('Deleted OK', true);
    else
        $this->message('Nothing deleted');
}

protected function action_save() {
    try {
        $this->db->query('insert into permission
          set permission = :permission
          on duplicate key update permission = :permission',
          array('permission' => $_POST['permission']));
    }
    catch (\Exception $e) {
        $this->show_form($_POST);
        throw $e;
    }
    $this->action_detail();
    if (isset($exc))
        throw $exc;
    $this->message('Saved OK', true);
}

protected function show_form($row) {
    $f = new Form();
    $f->start($row);
    $f->text('permission', 'Permission:', 30, 'permission');
    $f->button('action_save', 'Save');
    $f->end();
}

}

$page = new MyPage('Permission', true, array('admin', 'query'));
$page->go();

?>
