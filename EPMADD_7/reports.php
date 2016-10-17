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
require_once('lib/common.php');
require_once 'lib/Report.php';

class MyPage extends Page {
    
protected function request() {
    $stmt = $this->db->query('select * from query order by title');
    while ($row = $stmt->fetch()) {
        if ($this->ac->has_permission($row['permission'])) {
            echo "<br>";
            $this->button('Screen', array('action_run' => 1,
              'dest' => 'screen', 'query_id' => $row['query_id']));
            $this->button('PDF', array('action_run' => 1,
              'dest' => 'pdf', 'query_id' => $row['query_id']));
            $this->button('CSV', array('action_run' => 1,
              'dest' => 'csv', 'query_id' => $row['query_id']));
            echo '&nbsp;&nbsp;' . htmlspecial($row['title']);
        }
    }
}

protected function action_run() {
    $stmt = $this->db->query('select * from query
      where query_id = :query_id',
      array('query_id' => $_POST['query_id']));
    if ($row = $stmt->fetch()) {
        if (stripos($row['query'], 'file ') === 0)
            $this->transfer(substr($row['query'], 5),
              array('dest' => $_POST['dest']));
        else {
            $stmt2 = $this->db->query($row['query']);
            $r = new Report();
            if ($_POST['dest'] == 'screen')
                $r->html($row['title'], $stmt2);
            else if ($_POST['dest'] == 'pdf')
                $r->pdf($row['title'], $stmt2);
            else if ($_POST['dest'] == 'csv')
                $r->csv($stmt2);
        }
    }
    else
        $this->message('Query not found');
}

}

$page = new MyPage('Reports');
$page->go();

?>
