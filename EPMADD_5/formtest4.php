<?php
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
echo <<<EOT
<style>
label:before {
    content:"\a";
    white-space: pre;
}
</style>
EOT;
require_once('lib/common.php');
$db = new EPMADD\DbAccess();
$data = array();

foreach ($_REQUEST as $name => $value)
    if (strpos($name, 'action_') === 0) {
        $data = $name($db);
        break;
    }
show_form($data);

function action_find($db) {
    $last = empty($_REQUEST['last']) ? '' : $_REQUEST['last'];
    $stmt = $db->query('select member_id, last, first from
      member where last like :pat',
      array('pat' => "$last%"));
    if ($row = $stmt->fetch()) {
        return $row;
    }
    echo "<p>Not found";
    return array();
}
    
function action_new($db) {
    return array();
}
    
function action_save($db) {
    $db->update('member', 'member_id',
      array('last', 'first'), $_REQUEST);
    echo "saved";
    return $_REQUEST;
}

function show_form($data) {
    $member_id = empty($data['member_id']) ? '' :
      $data['member_id'];
    $last = empty($data['last']) ? '' :
      $data['last'];
    $first = empty($data['first']) ? '' :
      $data['first'];
    echo <<<EOT
        <form action='{$_SERVER['PHP_SELF']}' method=post
          accept-charset=UTF-8>
        <label for=last>Last:</label>
        <input type=text size=50 name=last id=last
          value='$last'>
        <label for=first>First:</label>
        <input type=text size=50 name=first id=first
          value='$first'>
        <input type=hidden name=member_id value='$member_id'
          hidden>
        <br>
        <input type=submit name=action_find value='Find'>
        <input type=submit name=action_new value='New'>
        <input type=submit name=action_save value='Save'>
        </form>
EOT;
}

?>
