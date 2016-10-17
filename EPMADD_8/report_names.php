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
set_time_limit(1000);

class MyPage extends Page {

protected function request() {
    for ($i = ord('A'); $i <= ord('Z'); $i++) {
        $x = chr($i);
        $this->button($x, array('action_letter' => 1,
          'letter' => $x));
    }

/* For demonstration purposes.
echo '<br>' . JaroWinkler('McMillen', 'MacMillen', true);
echo '<br>' . JaroWinkler('David', 'Dave', true);
echo '<br>' . JaroWinkler('apples', 'oranges', true);
echo '<br>' . JaroWinkler('watermelon', 'sharkskin', true);
*/
}

protected function action_letter() {
    $this->do_letter($_REQUEST['letter']);
}

protected function do_letter($letter) {
    $found = false;
    $skip = array();
    $stmt = $this->db->query('select person_pk, name_last,
      name_first, name_middle
      from person where name_last like :letterpat and
      replacedby_fk is null
      order by name_last, name_first, name_middle',
      array('letterpat' => "$letter%"));
    while ($row = $stmt->fetch()) {
        if (!in_array($row['person_pk'], $skip)) {
            $names = array($this->build_name($row));
            $pks = array($row['person_pk']);
            $this->find_matches($row['person_pk'],
              $row['name_last'], $row['name_first'],
              $row['name_middle'], $names, $pks);
            if (count($names) > 1) {
                for ($i = 0; $i < count($names); $i++) {
                    $pkstring = '';
                    foreach ($pks as $p)
                        if ($p != $pks[$i])
                            $pkstring .= ',' . $p;
                    $pkstring = substr($pkstring, 1);
                    echo "<br>{$names[$i]}";
                    $this->button('Choose',
                      array('action_choose' => 1,
                      'pk' => $pks[$i],
                      'others' => $pkstring),
                      'persons_link.php', true);
                    $this->button('View',
                      array('action_detail' => 1,
                      'pk' => $pks[$i]),
                      'person.php', true);
                    $found = true;
                }
                $skip = array_merge($skip, $pks);
                echo '<hr>';
            }
        }
    }
    if (!$found)
        echo "<p>Letter {$letter}: No persons found.";
}

protected function find_matches($pk, $last, $first, $middle,
  &$names, &$pks) {
    if (strlen($last) < 2)
        return;
    $pfx = mb_substr($last, 0, 2, 'UTF-8');
    $stmt = $this->db->query('select person_pk, name_last,
      name_first, name_middle
      from person where name_last like :pfxpat and
      person_pk != :pk and
      replacedby_fk is null order by name_last, name_first,
      name_middle',
      array('pfxpat' => "$pfx%", 'pk' => $pk));
    while ($row = $stmt->fetch()) {
        $jw1 = JaroWinkler($last, $row['name_last'], true);
        if (empty($first))
            $jw2 = $jw3 = $jw4 = 1;
        else {
            $name1 = explode(' ', trim($first));
            $name2 = explode(' ', trim($row['name_first']));
            $jw2 = JaroWinkler($name1[0], $name2[0], true);
            $jw3 = JaroWinkler($name1[0], $row['name_middle'],
              true);
            $jw4 = JaroWinkler($name2[0], $middle, true);
        }
        if ($jw1 > .9 && ($jw2 > .75 || $jw3 > .75 ||
          $jw4 > .75)) {
            $names[] = $this->build_name($row);
            $pks[] = $row['person_pk'];
        }
    }
}

protected function build_name($row) {
    return htmlspecial(trim(
      "{$row['name_last']}, {$row['name_first']} {$row['name_middle']}"
      ));
}

}

$page = new MyPage('Variant Names');
$page->getdb()->set_database('cwadb');
$page->go();

?>
