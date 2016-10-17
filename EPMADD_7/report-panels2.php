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

function specialties() {
    $this->db->query('update member set specialty_id =  FLOOR(1 + RAND() * 5)');
}

protected function request() {
    $f = new Form();
    $f->start($_POST);
    $f->radio('dest', 'Screen', 'screen');
    $f->hspace(2);
    $f->radio('dest', 'PDF', 'pdf', false);
    $f->hspace(2);
    $f->radio('dest', 'CSV', 'csv', false);
    $f->text('year', 'Year:', 30, 'YYYY');
    $f->button('action_report', 'Report', false);
    $f->end();
}

protected function action_report() {
    $hdgs = array('Number', 'Date', 'Start', 'Stop', 'Title');
    $ttl = "{$_POST['year']} Panels";
    $stmt = $this->db->query('select number, date_held,
      time_start, time_stop, title from panelcwa
      where year = :year order by number',
      array('year' => $_POST['year']));
    if ($stmt->rowCount() == 0)
        $this->message('No records found', true);
    else {
        $r = new Report();
        if ($_POST['dest'] == 'screen')
            $r->html($ttl, $stmt, $hdgs);
        else if ($_POST['dest'] == 'pdf')
            $r->pdf($ttl, $stmt, array(50, 50, 50, 50), $hdgs);
        else if ($_POST['dest'] == 'csv')
            $r->csv($stmt);
    }
}

}

$page = new MyPage('Panels Report', true, 'panels-view');
$page->go();

?>
