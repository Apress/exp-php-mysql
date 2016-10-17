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
require_once 'lib/Report.php';

class MyPage extends Page {
    
protected function request() {
    echo <<<EOT
    <style>
    .name {
        margin: 0;
        margin-left: 10px;
        font-style: italic;
        font-weight: bold;
    }
    .query {
        margin: 0;
        margin-left: 10px;
        margin-bottom: 10px;
        color: gray;
    }
    .run {
        overflow: auto;
        max-height: 200px;
        width: 600px;
        border: 1px solid;
        padding: 5px;
    }
    .query-table {
        width: 600px;
    }
    </style>
	<script>
	
    function DeleteQuery(pk, name) {
        if (confirm('Delete query "' + name + '"?'))
            transfer('query.php', {'action_delete': 1, 'pk': pk});
    }

    function RunQuery(pk) {
        transfer('query.php', {'action_run': 1, 'pk': pk});
    }

    function EditQuery(pk) {
        transfer('query.php', {'action_edit': 1, 'pk': pk});
    }
	
	</script>
EOT;
    $this->action('action_new');
}

protected function action_new() {
    $this->show_form();
}

protected function action_edit($run = false) {
    $stmt = $this->db->query('select * from query
      where query_id = :query_id',
      array('query_id' => $_POST['pk']));
    if ($stmt->rowCount() == 0)
        $this->message('Failed to retrieve record.');
    $row = $stmt->fetch();
    $this->show_form($row, $run);
}

protected function action_run() {
    $this->action_edit(true);
}

protected function show_form($data = null, $run = false) {
    if (empty($data['category']))
        $data['category'] = 'General';
    $f = new Form();
    $f->start($data);
    if (isset($data['query_id']))
        $f->hidden('query_id', $data['query_id']);
    $f->text('title', 'Query Title:', 70, 'query title');
    $f->textarea('query', 'Query:', 80, 3);
    $f->text('category', 'Category:', 30, 'category');
    $f->menu('permission', 'Permission:',
      $this->ac->get_permissions(), false, 'query');
    $f->button('action_save', 'Save');
    $f->button('action_save_run', 'Save & Run', false);
    $f->hspace(30);
    $f->button('action_new', 'New', false);
    $f->end();
    if ($run && isset($data['query']))
        if (stripos($data['query'], 'file ') === 0)
            $this->message("Can't run file reports here");
        else
            $this->run($data['title'], $data['query']);
    echo "<p style='margin-top:20px;'>";
    $this->query_list();
}

protected function run($title, $sql) {
    echo '<div class=run>';
    $stmt = $this->db->query($sql);
    $r = new Report();
    $r->html($title, $stmt);
    echo '</div>';
}

protected function action_save($run = false) {
    if (stripos($_POST['query'], 'select ') !== 0 &&
      stripos($_POST['query'], 'file ') !== 0) {
        $this->message('Only select or file queries are allowed');
        $this->show_form($_POST);
        return;
    }
    try {
        $pk = $this->db->update('query', 'query_id',
          array('category', 'title', 'query', 'permission'), $_POST);
    }
    catch (\Exception $e) {
        $this->show_form($_POST);
        throw $e;
    }
    $this->action_detail($pk, $run);
    if (isset($exc))
        throw $exc;
    $this->message('Saved OK', true);
}

protected function action_save_run() {
    $this->action_save(true);
}

protected function action_detail($pk = null, $run = false) {
    if (is_null($pk))
        $pk = $_POST['query_id'];
    $stmt = $this->db->query('select * from query
      where query_id = :query_id',
      array('query_id' => $pk));
    if ($stmt->rowCount() == 0)
        $this->message('Failed to retrieve record.');
    $row = $stmt->fetch();
    $this->show_form($row, $run);
}

protected function action_delete() {
    $stmt = $this->db->query('delete from query where
      query_id = :query_id',
      array('query_id' => $_REQUEST['pk']));
    if ($stmt->rowCount() == 1)
        $this->message('Deleted OK', true);
    else
        $this->message('Nothing deleted');
    $this->show_form();
}

function query_list() {
	$stmt = $this->db->query('select * from query
      order by category, title');
    $cat = null;
    while ($row = $stmt->fetch()) {
        if ($cat != $row['category']) {
            if (!is_null($cat))
                echo "</table>";
            echo "<h2>{$row['category']}</h2>";
            echo "<table class=query-table>";
            $cat = $row['category'];
        }
        echo "<tr>";
        echo "<td nowrap valign=top>";
        echo "<button type=button class=button onclick=
          'RunQuery(\"{$row['query_id']}\")'>Run</button>";
        echo "<button type=button class=button onclick=
          'EditQuery(\"{$row['query_id']}\")'>Edit</button>";
        echo "<button type=button class=button onclick=
          'DeleteQuery(\"{$row['query_id']}\",
          \"{$row['title']}\")'>Delete</button>";
        $t = htmlspecial($row['title']);
        $q = htmlspecial($row['query']);
        echo "<td width=100% valign=top>
          <p class=name>$t<p class=query>$q";
    }
    echo "</table>";
}

}

$page = new MyPage('Queries', true, 'query');
$page->go();

?>
