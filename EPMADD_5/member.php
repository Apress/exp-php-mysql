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
    
/*
protected $query = 'select * from member where member_id = :member_id';
*/
protected $query = 'select * from member left join specialty
  using (specialty_id) where member_id = :member_id';

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
            echo "<button class=button onclick=\"DeleteConfirm('$name', '{$row['member_id']}');\">Delete</button>";
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
    $stmt = $this->db->query($this->query,
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
        $pk = $this->db->update('member', 'member_id',
          array('last', 'first', 'street',
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

protected function action_add_interest() {
    try {
        $this->db->update('interest', 'interest_id',
          array('name', 'member_id'),
          array('name' => $_POST['interest'],
          'member_id' => $_POST['member_id']));
    }
    catch (\Exception $e) {
        $exc = $e;
    }
    $this->action_detail();
    if (isset($exc))
        throw $exc;
}

protected function action_delete_interest() {
    try {
        if (isset($_POST['interests'])) {
            $this->db->query('delete from interest where
              interest_id = :interest_id',
              array('interest_id' => $_POST['interests']));
        }
    }
    catch (\Exception $e) {
        $exc = $e;
    }
    $this->action_detail();
    if (isset($exc))
        throw $exc;
}

protected function action_delete_specialty() {
    try {
        if (isset($_POST['specialties'])) {
            $this->db->query('delete from member_specialty
              where member_id = :member_id and
              specialty_id = :specialty_id',
              array('member_id' => $_POST['member_id'],
              'specialty_id' => $_POST['specialties']));
        }
    }
    catch (\Exception $e) {
        $exc = $e;
    }
    $this->action_detail();
    if (isset($exc))
        throw $exc;
}

/*
protected function show_form($row) {
    echo "<form action='{$_SERVER['PHP_SELF']}'
      method=post accept-charset=UTF-8>";
    foreach (array('member_id', 'last', 'first', 'street',
      'city', 'state', 'specialty_id') as $col) {
        if ($col == 'member_id')
            $type = 'hidden';
        else {
            echo "<p class=label>$col: ";
            $type = 'text';
        }
        $v = is_null($row) ? '' : $row[$col];
        echo "<input type=$type id=$col size=50 name=$col
          value='" . htmspecial($v) .
          "'>";
    }
    echo "<p class=label><input class=button type=submit
      name=action_save value=Save></form>";
}
*/
/*
protected function show_form($row) {
    echo "<form action='{$_SERVER['PHP_SELF']}'
      method=post accept-charset=UTF-8>";
    foreach (array('member_id', 'last', 'first', 'street',
      'city', 'state', 'specialty_id', 'name') as $col) {
        if ($col == 'name')
            $readonly = 'readonly';
        else
            $readonly = '';
        $id = $col == 'name' ? 'specialty_id_label' : $col;
        if ($col == 'member_id' || $col == 'specialty_id')
            $type = 'hidden';
        else {
            echo "<label for=$id>$col:</label>";
            $type = 'text';
        }
        $v = is_null($row) ? '' : $row[$col];
        echo "<input type=$type id=$id size=50 name=$col
          value='" . htmspecial($v) .
          "' $readonly>";
        if ($col == 'name') {
            echo "<button class=button type=button
              onclick='ChooseSpecialty(\"specialty_id\");'>
              Choose...</button>";
            echo "<button class=button type=button
              onclick='ClearField(\"specialty_id\");'>
              Clear</button>";
        }
    }
    echo "<p class=label><input class=button type=submit
      name=action_save value=Save></form>";
}
*/

protected function show_form($row) {
    echo '<table border=0 cellspacing=0 cellpadding=20px><tr><td>';
    $this->show_form_left($row);
    echo '<td style="border-left: 1px solid gray;">';
    $this->show_form_right($row);
    echo '</table>';
}

protected function show_form_left($row) {
    echo "<form action='{$_SERVER['PHP_SELF']}'
      method=post accept-charset=UTF-8>";
    foreach (array('member_id', 'last', 'first', 'street',
      'city', 'state') as $col) {
        if ($col == 'member_id')
            $type = 'hidden';
        else {
            echo "<label for=$col>$col:</label>";
            $type = 'text';
        }
        $v = is_null($row) ? '' : $row[$col];
        echo "<input type=$type size=50 name=$col id=$col
          value='" . htmspecial($v) . "'>";
    }
    echo <<<EOT
EOT;
    echo "<p class=label><input class=button type=submit
      name=action_save value=Save></form>";
}

protected function show_form_right($member) {
    $member_id = $member['member_id'];
    echo <<<EOT
        Specialties
        <form action='{$_SERVER['PHP_SELF']}'
          method=post accept-charset=UTF-8>
EOT;
    if (isset($member_id)) {
        $stmt = $this->db->query('select specialty_id, name
            from specialty
            join member_specialty using (specialty_id)
            where member_id = :member_id',
          array('member_id' => $member_id));
        echo '<select name=specialties size=10
          style="min-width:100px;">';
        while ($row = $stmt->fetch())
            echo "<option
            value={$row['specialty_id']}>{$row['name']}</option>";
        echo '</select>';
    }
    echo <<<EOT
    <br><input class=button type=submit
      name=action_delete_specialty value='Delete Selected'>
    <br><input class=button type=button
      value='Add'
      onclick='ChooseSpecialty($member_id);'>
    <input type=hidden name=member_id value=$member_id>
    </form>
EOT;
}

/*
protected function show_form_left($row) {
    echo "<form action='{$_SERVER['PHP_SELF']}'
      method=post accept-charset=UTF-8>";
    foreach (array('member_id', 'last', 'first', 'street',
      'city', 'state', 'specialty_id', 'name') as $col) {
        if ($col == 'specialty_id' || $col == 'name')
            $readonly = 'readonly';
        else
            $readonly = '';
        if ($col == 'member_id' || $col == 'specialty_id')
            $type = 'hidden';
        else {
            echo "<p class=label>$col: ";
            $type = 'text';
        }
        $v = is_null($row) ? '' : $row[$col];
        $id = $col == 'name' ? 'specialty_id_label' : $col;
        echo "<input type=$type id=$id size=50 name=$col
          value='" . htmspecial($v) .
          "' $readonly>";
        if ($col == 'name') {
            echo "<button class=button type=button
              onclick='ChooseSpecialty(\"specialty_id\");'>
              Choose...</button>";
            echo "<button class=button type=button
              onclick='ClearField(\"specialty_id\");'>
              Clear</button>";
        }
    }
    echo <<<EOT
EOT;
    echo "<p class=label><input class=button type=submit
      name=action_save value=Save></form>";
}

protected function show_form_right($member) {
    $member_id = $member['member_id'];
    echo <<<EOT
        <p class=label>Interersts
        <form action='{$_SERVER['PHP_SELF']}'
          method=post accept-charset=UTF-8>
EOT;
    if (isset($member_id)) {
        $stmt = $this->db->query('select interest_id, name from interest where member_id = :member_id',
          array('member_id' => $member_id));
        echo '<select name=interests size=10 style="min-width:100px;">';
        while ($row = $stmt->fetch()) {
            echo "<option value={$row['interest_id']}>{$row['name']}</option>";
        }
        echo '</select>';
    }
    echo <<<EOT
    <p class=label><input class=button type=submit
      name=action_delete_interest value='Delete Selected'>
    <p class=label><input class=button type=submit
      name=action_add_interest value='Add'>
    <input type=text id=interest size=50 name=interest
      value='' placeholder=Interest>
    <input type=hidden name=member_id value=$member_id>
    </form>
EOT;
}
*/
}

$page = new MyPage('Member');
$page->go();

?>
