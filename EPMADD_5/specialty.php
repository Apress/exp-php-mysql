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
    
protected $query = 'select * from specialty
  where specialty_id = :specialty_id';

protected function request() {
	echo <<<EOT
<form action="{$_SERVER['PHP_SELF']}" method=post accept-charset=UTF-8>
<p class=label>
Name:
<input type=text size=50 name=name placeholder='Name'>
<input class=button type=submit name=action_find value='Find'>
<input class=button type=submit name=action_new value='New'>
EOT;
	if (isset($_REQUEST["choose"]))
		echo "<input type=hidden name=choose value=yes>";
	if (isset($_REQUEST["id"]))
		echo "<input type=hidden name=id value='{$_REQUEST['id']}'>";
    echo '</form>';
}

/*
protected function action_find($pat = null) {
    if (is_null($pat))
        $pat = "{$_POST['name']}%";
    $stmt = $this->db->query('select specialty_id, name from specialty where name like :pat',
        array('pat' => $pat));
*/

protected function action_find() {
    if (isset($_REQUEST["choose"]))
        $this->action_find_choices();
    else
        $this->action_find_normal();
}

protected function action_find_choices() {
    $url = $_SERVER['PHP_SELF'];
    $member_id = $_REQUEST['id'];
    $stmt = $this->db->query('select specialty.specialty_id, name
      from specialty
      left join member_specialty on
      specialty.specialty_id = member_specialty.specialty_id and
      :member_id = member_specialty.member_id
      where name like :pat and member_id is null',
      array('pat' => "{$_POST['name']}%",
      'member_id' => $member_id));
    if ($stmt->rowCount() == 0)
        $this->message('No unchosen specialties found', true);
    else {
        echo <<<EOT
            <p>Unchosen Specialties
            <form action=$url method=post>
EOT;
        while ($row = $stmt->fetch()) {
            $name = $row['name'];
            $pk = $row['specialty_id'];
            echo <<<EOT
                <p class=find-choice>
                <input type='checkbox' name=specialty[$pk]>
                &nbsp;&nbsp;$name
EOT;
        }
            echo <<<EOT
            <p>
            <input type=hidden name=member_id value=$member_id>
           <input class=button type=submit
              name=action_add value='Add Specialties'>
            </form>
EOT;
    }
}

protected function action_find_normal() {
    $url = $_SERVER['PHP_SELF'];
    $stmt = $this->db->query('select specialty_id, name
      from specialty where name like :pat',
      array('pat' => "{$_POST['name']}%"));
    if ($stmt->rowCount() == 0)
        $this->message('No records found', true);
    else {
        echo '<p>';
        while ($row = $stmt->fetch()) {
            $name = $row['name'];
            $pk = $row['specialty_id'];
            echo '<p class=find-choice>';
            if (isset($_REQUEST["choose"]))
                echo "<button class=button
                  onclick='MadeChoice(\"{$_REQUEST['id']}\",
                  \"$pk\", \"$name\");'>Choose</button>";
            else {
                echo <<<EOT
            <p class=find-choice>
            <a href=$url?action_detail&specialty_id=$pk>Detail</a>
            <a href=''
            onclick="DeleteConfirm('$name', '$pk');">Delete</a>
EOT;
            }
            echo "&nbsp;&nbsp;$name";
        }
    }
}

protected function action_add() {
    if (isset($_REQUEST['specialty'])) {
        foreach ($_REQUEST['specialty'] as $specialty_id => $v)
            $this->db->query('insert into member_specialty
              (member_id, specialty_id)
              values (:member_id, :specialty_id)',
              array('member_id' => $_REQUEST['member_id'],
              'specialty_id' => $specialty_id));
        $this->message('Added OK. Window may be closed.',
          true);
    }
    else
        $this->message('No specialties were added.');
}

protected function action_find1() {
    $url = $_SERVER['PHP_SELF'];
    $stmt = $this->db->query('select specialty_id, name
      from specialty where name like :pat',
      array('pat' => "{$_POST['name']}%"));
    if ($stmt->rowCount() == 0)
        $this->message('No records found', true);
    else {
        echo '<p>';
        while ($row = $stmt->fetch()) {
            $name = $row['name'];
            $pk = $row['specialty_id'];
            echo '<p class=find-choice>';
            if (isset($_REQUEST["choose"]))
                echo "<button class=button
                  onclick='MadeChoice(\"{$_REQUEST['id']}\",
                  \"$pk\", \"$name\");'>Choose</button>";
            else {
                echo <<<EOT
            <p class=find-choice>
            <a href=$url?action_detail&pk=$pk>Detail</a>
            <a href=''
            onclick="DeleteConfirm('$name', '$pk');">Delete</a>
EOT;
            }
            echo "&nbsp;&nbsp;$name";
        }
    }
}

protected function action_find2() {
    $stmt = $this->db->query('select specialty_id, name
      from specialty where name like :pat',
      array('pat' => "{$_POST['name']}%"));
    if ($stmt->rowCount() == 0)
        $this->message('No records found', true);
    else {
        echo '<p>';
        while ($row = $stmt->fetch()) {
            $name = $row['name'];
            echo '<p class=find-choice>';
            if (isset($_REQUEST["choose"]))
                echo "<button class=button
                  onclick='MadeChoice(\"{$_REQUEST['id']}\",
                  \"{$row['specialty_id']}\",
                  \"{$row['name']}\");'>Choose</button>";
            else {
                $this->button('Detail',
                  array('action_detail' => 1,
                  'specialty_id' => $row['specialty_id']),
                  'specialty.php');
                echo "<button class=button onclick=\"DeleteConfirm('$name', '{$row['specialty_id']}');\">Delete</button>";
            }
            echo "&nbsp;&nbsp;$name";
        }
    }
}

protected function action_new() {
    $this->show_form(null);
}

protected function action_detail($pk = null) {
    if (is_null($pk))
        $pk = $_REQUEST['specialty_id'];
    $stmt = $this->db->query($this->query,
      array('specialty_id' => $pk));
    if ($stmt->rowCount() == 0)
        $this->message('Failed to retrieve record.');
    $row = $stmt->fetch();
    $this->show_form($row);
}

protected function action_delete() {
    $stmt = $this->db->query('delete from specialty where
      specialty_id = :specialty_id',
      array('specialty_id' => $_REQUEST['pk']));
    if ($stmt->rowCount() == 1)
        $this->message('Deleted OK', true);
    else
        $this->message('Nothing deleted');
}

protected function action_save() {
    try {
        if (empty($_POST['specialty_id'])) // form supplies ''
            unset($_POST['specialty_id']);
        $pk = $this->db->update('specialty', 'specialty_id',
          array('specialty_id', 'name'), $_POST);
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
    foreach (array('specialty_id', 'name') as $col) {
        if ($col == 'specialty_id')
            $type = 'hidden';
        else {
            echo "<p class=label>$col: ";
            $type = 'text';
        }
        $v = is_null($row) ? '' : $row[$col];
        echo "<input type=$type id=$col size=50 name=$col
          value='" . htmlentities($v, ENT_QUOTES, 'UTF-8') .
          "'>";
    }
    echo "<p class=label><input class=button type=submit
      name=action_save value=Save></form>";
}

}

$page = new MyPage('Specialty');
$page->go();

?>
