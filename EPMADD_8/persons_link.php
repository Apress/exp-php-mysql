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

// Chapter 5 code not updated for Chapter 6
define('CHAPTER_5', true);

set_include_path('../EPMADD_6:../EPMADD_7:../EPMADD_8');
require_once 'lib/common.php';
require_once 'JaroWinkler.php';
use PDOException;

class MyPage extends Page {

protected function action_choose() {
    $others = explode(",", $_POST['others']);
    $chosen_name = $this->GetNameByID($_POST['pk']);
    $f = new Form();
    $f->start($_POST);
    $f->hidden('pk', $_POST['pk']);
    echo <<<EOT
        <p>Do you want this person:
        <p style='margin-left:20px;'>$chosen_name
        <p>to replace these checked persons?
EOT;
    foreach ($others as $p)
        $f->checkbox("replace[$p]", $this->GetNameByID($p));
    echo <<<EOT
        <p>The replaced persons will not be deleted,
        so you can copy<br>any required data into the person
        that replaces them.
EOT;
    $f->button('action_replace', 'Replace');
    echo "<button class=button type=button
      onclick='window.close();'>Cancel</button>";
    $f->end();
}

protected function action_replace() {
    if (empty($_POST['replace'])) {
        $this->message('No replacements were checked.');
        return;
    }
    $this->db->query("begin");
    $pk = $_POST['pk'];
    foreach ($_POST['replace'] as $p => $v) {
        echo '<p>"' . $this->GetNameByID($pk) . '" will replace "' .
          $this->GetNameByID($p) . '"';
        $this->replace($pk, $p, 'donation', 'donor1_fk');
        $this->replace($pk, $p, 'donation', 'donor2_fk');
        $this->replace($pk, $p, 'house', 'committee_contact_fk');
        $this->replace($pk, $p, 'invitation', 'invitee_fk');
        $this->replace($pk, $p, 'panel', 'moderator_fk');
        $this->replace($pk, $p, 'panel', 'producer1_fk');
        $this->replace($pk, $p, 'panel', 'producer2_fk');
        $this->replace($pk, $p, 'person', 'committee_contact_fk');
        $this->replace($pk, $p, 'person', 'companion_to_fk');
        $this->replace($pk, $p, 'person', 'contact_fk');
        $this->replace($pk, $p, 'person', 'hyphen_fk');
        $this->replace($pk, $p, 'person', 'introduced_by_fk');
        $this->replace($pk, $p, 'status', 'person_fk');
        $this->replace($pk, $p, 'topic', 'participant_fk');
        $this->replace($pk, $p, 'trip', 'driver_arrival_fk');
        $this->replace($pk, $p, 'trip', 'driver_departure_fk');
        $this->replace($pk, $p, 'trip', 'participant1_fk');
        $this->replace($pk, $p, 'trip', 'participant2_fk');
        $this->replace($pk, $p, 'venue', 'contact_fk');
        $this->link_person($pk, $p);
    }
$this->db->query("rollback"); // this and next 2 lines for testing
$this->message('All updates were rolled back.');
return;
    $this->db->query("commit");
    $this->message('All updates were successful.', true);
}

/*
            $result = $this->db->query(
                "select table_name, column_name
                from information_schema.key_column_usage
                where referenced_table_name = 'person' and
                referenced_column_name = 'person_pk' and
                table_schema = 'cwadb'
                order by table_name, column_name");
            while ($row = $result->fetch())
                replace($chosen, $p,
                  $row['table_name'], $row['column_name']);
*/

protected function GetNameByID($x) {
    $stmt = $this->db->query('select name_first, name_middle, name_last from person where person_pk = :pk',
      array('pk' => $x));
    if ($row = $stmt->fetch())
        $x = $this->GetName($row);
    return $x;
}

protected function GetName(&$row) {
    return htmlspecial(trim("{$row['name_last']}, {$row['name_first']} {$row['name_middle']}"));
}

protected function replace($pk, $p, $table, $col) {
  $this->db->query("update $table set $col = :pk where $col = :p",
    array('pk' => $pk, 'p' => $p));
    echo "<p class=replace-msg>$table.$col updated</p>";
}

protected function link_person($pk, $p) {
    $this->db->query('update person set replacedby_fk = :pk
      where person_pk = :p',
      array('pk' => $pk, 'p' => $p));
    echo "<p class=replace-msg>replaced person linked to
      replacing person</p>";
}

}

$page = new MyPage('Person Link');
$page->getdb()->set_database('cwadb');
$page->go();
/*
echo <<<EOT
    <style>
        .replace-msg {
            margin-left: 20px;
            margin-top: 0;
            margin-bottom: 0;
            color: gray;
        }
    </style>
EOT;
hide_div_request();
*/

/*
else { // $_POST['others'] not set
}
// else {
//  $a = explode(",", $_REQUEST['others']);
//  echo "<form name=input1 action=\"persons_link.php?replace=yes&pk={$_REQUEST['pk']}\" method=post accept-charset=UTF-8>";
//  echo "<p>Do you want this person:";
//  echo "<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . GetNameAndID($mysqli, $_REQUEST['pk']);
//  echo "<p>to replace these checked persons?";
//  foreach ($a as $p) {
//      echo "<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
//      echo "<input type=checkbox name=pk=$p>";
//      echo GetNameAndID($mysqli, $p);
//  }
//  echo "<p>The replaced persons will not be deleted, so you can copy any required data into the person that replaces them.";
//  echo "<p>";
//  echo "<input class=button type=submit value='Replace'>";
//  echo "<button class=button type=button onclick='window.close();'>Cancel</button>";
//  echo "</form>";
// }
*/
?>
