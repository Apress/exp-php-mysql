<?php
namespace EPMADD;
/*
	Many of the code files for Chapter 5 (folder EPMADD_5) have better
	versions in EPMADD_6, EPMADD_7, or EPMADD_8. For your own
	applications, take the code from there rather than here.
*/
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
	echo <<<EOT
<form action="{$_SERVER['PHP_SELF']}"
  method=post accept-charset=UTF-8>
<label for=last>Last Name:</label>
<input type=text size=50 name=last id=last
  placeholder='Last Name'>
<input class=button type=submit name=action_find value='Find'>
<br>
<input class=button type=submit name=action_new value='New'>
</form>
EOT;
}

protected function action_find() {
    $url = $_SERVER['PHP_SELF'];
    $stmt = $this->db->query('select member_id, last, first
      from member where last like :pat',
      array('pat' => "{$_POST['last']}%"));
    if ($stmt->rowCount() == 0)
        //$this->message('No records found', true);
        echo <<<EOT
    	<script>
		$(document).ready(function () {
			$('#div-message').css('padding', '10px');
			$('#message-error').html("No records found");
		});
        </script>
EOT;
    else {
        echo '<p>';
        while ($row = $stmt->fetch()) {
            $name = "{$row['last']}, {$row['first']}";
            $pk = $row['member_id'];
            echo <<<EOT
            <p class=find-choice>
            <a href=$url?action_detail&pk=$pk>Detail</a>
            <a href=''
            onclick="DeleteConfirm('$name', '$pk');">Delete</a>
            &nbsp;&nbsp;$name
EOT;
        }
    }
}

protected function action_find1() {
    $url = $_SERVER['PHP_SELF'];
    $stmt = $this->db->query('select member_id, last, first
      from member where last like :pat',
      array('pat' => "{$_POST['last']}%"));
    if ($stmt->rowCount() == 0)
        //$this->message('No records found', true);
        echo <<<EOT
    	<script>
		$(document).ready(function () {
			$('#div-message').css('padding', '10px');
			$('#message-error').html("No records found");
		});
        </script>
EOT;
    else {
        echo '<p>';
        while ($row = $stmt->fetch()) {
            $name = "{$row['last']}, {$row['first']}";
            $pk = $row['member_id'];
            echo <<<EOT
            <p class=find-choice>
            <a href=$url?action_detail&pk=$pk>Detail</a>
            <a href=$url?action_delete&pk=$pk>Delete</a>
            &nbsp;&nbsp;$name
EOT;
        }
    }
}

protected function action_find2() {
    $stmt = $this->db->query('select member_id, last, first
      from member where last like :pat',
      array('pat' => "{$_POST['last']}%"));
    if ($stmt->rowCount() == 0)
        $this->message('No records found', true);
    else {
        echo '<p>';
        while ($row = $stmt->fetch()) {
            $name = "{$row['last']}, {$row['first']}";
            echo '<p class=find-choice>';
            $this->button('Detail',
              array('action_detail' => 1,
              'pk' => $row['member_id']), $_SERVER['PHP_SELF']);
            echo "<button class=button
              onclick=\"DeleteConfirm('$name',
              '{$row['member_id']}');\">Delete</button>";
            echo "&nbsp;&nbsp;$name";
        }
    }
}

protected function action_new() {
    $this->show_form(null);
}

protected function action_detail($pk = null) {
    if (is_null($pk))
        $pk = $_REQUEST['pk'];
    $stmt = $this->db->query('select * from member
      where member_id = :member_id',
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
        $pk = $this->db->update('member', 'member_id',
          array('member_id', 'last', 'first', 'street',
          'city', 'state'), $_POST);
    }
    catch (\Exception $e) {
        $this->show_form($_POST);
        throw $e;
    }
    $this->action_detail($pk);
    $this->message('Saved OK', true);
}

protected function action_save2() {
    try {
        if (empty($_POST['specialty_id'])) // form supplies ''
            unset($_POST['specialty_id']);
        $pk = $this->db->update('member', 'member_id',
          array('member_id', 'last', 'first', 'street',
          'city', 'state', 'specialty_id'), $_POST);
    }
    catch (\Exception $e) {
        $exc = $e;
        $pk = null;
    }
    $this->action_detail($pk);
    if (isset($exc))
        throw $exc;
    $this->message('Saved OK', true);
}

protected function show_form($row) {
    echo "<form action='{$_SERVER['PHP_SELF']}'
      method=post accept-charset=UTF-8>";
    foreach (array('member_id', 'last', 'first', 'street',
      'city', 'state') as $col) {
        if ($col == 'member_id')
            $type = 'hidden';
        else {
            echo "<label for$col>$col:</label>";
            $type = 'text';
        }
        $v = is_null($row) ? '' : $row[$col];
        echo "<input type=$type id=$col size=50 name=$col
          value='" . htmspecial($v) . "'>";
    }
    echo "<br><input class=button type=submit
      name=action_save value=Save></form>";
}

}

$page = new MyPage('Member');
$page->go();

?>
