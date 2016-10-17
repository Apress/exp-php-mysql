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
require_once 'lib/fpdf17/fpdf.php';
require_once 'lib/fpdf-plus/mc_table.php';

class PDF extends \PDF_MC_Table {

protected $width;

function Header() {
    $this->SetX(-1); // negative arg means relative to right of page
    $this->width = $this->GetX() + 1;
    $this->SetFont('Helvetica', 'I', 8);
    $this->Text(10, 15, date('c'));
    $this->SetLineWidth(.1);
    $this->Line(0, 17, $this->width, 22);
    $this->SetY(30);
}

function Footer() {
    $this->SetY(-1); // negative arg means relative to bottom of page
    $page_height = $this->GetY() + 1;
    $this->SetLineWidth(.1);
    $this->Line(0, $page_height - 12, $this->width, $page_height - 12);
    $this->SetFont('Helvetica', 'I', 8);
    $this->Text(10, $page_height - 4, 'Page ' . $this->PageNo() .
      ' of {nb}');
}

}

class MyPage extends Page {
    
protected function request() {
    $f = new Form();
    $f->start($_POST);
    $f->text('year', 'Year:', 30, 'YYYY');
    $f->button('action_report', 'Report', false);
    $f->end();
}

protected function action_report() {
    $stmt = $this->db->query('select number, title, date_held, time_start, time_stop from panelcwa
      where year = :year order by number limit 5',
      array('year' => $_POST['year']));
    if ($stmt->rowCount() == 0)
        $this->message('No records found', true);
    else {
        $dir = 'output'; // previously created
        $path = "$dir/report-" . date('M-Y') . '-' . uniqid() . '.pdf';
        fclose(fopen($path, 'w'));
        chmod($path, 0644);

        $url = "http://" . $_SERVER['HTTP_HOST'] .
          dirname($_SERVER['REQUEST_URI']) . "/$path";
        $pdf = new PDF('L', 'pt', array(500, 400));
        $pdf->AliasNbPages();
        $pdf->SetFont('Helvetica', '' , 7);
        $pdf->SetLineWidth(.1);
        $pdf->SetMargins(10, 10);
        $pdf->SetAutoPageBreak(true, 30);
        $pdf->SetHorizontalPadding(2);
        $pdf->SetVerticalPadding(3);
        $pdf->SetStyles(array('B', '', '', '', 'I'));
        $pdf->SetWidths(array(70, 50, 50, 70, 140));
        $pdf->SetAligns(array('R', 'C', 'C', 'C', 'L'));
        $pdf->AddPage();
        while ($row = $stmt->fetch())
            $pdf->RowX(array($row['number'], $row['date_held'], $row['time_start'], $row['time_stop'], $row['title']));
        $pdf->Output($path, 'F');
        echo <<<EOT
        <p>Click below to access the report:
        <p><a href='$url'>$url</a>
EOT;
    }
}

/*
$dir = 'output'; // previously created
$path = "$dir/report-" . date('M-Y') . '-' . uniqid() . '.pdf';
fclose(fopen($path, 'w'));
chmod($path, 0644);

$url = "http://" . $_SERVER['HTTP_HOST'] .
  dirname($_SERVER['REQUEST_URI']) . "/$path";

$pdf = new FPDF();
$pdf->SetFont('Times', '', 20);
$pdf->AddPage();
$pdf->Text(25, 50, 'Lots of cash flow this month!');
$pdf->Output($path, 'F');

echo <<<EOT
<p>Click below to access the report:
<p><a href='$url'>$url</a>
EOT;
 * 
 * 
 *   'panel_pk' => string '3557' (length=4)
  'moderator_fk' => string '5203' (length=4)
  'producer1_fk' => null
  'producer2_fk' => null
  'venue_fk' => string '37' (length=2)
  'number' => string 'Series XVI' (length=10)
  'title' => string 'Redefining Art With a Small a' (length=29)
  'year' => string '1993' (length=4)
  'date_held' => string '1993-04-05' (length=10)
  'time_start' => string '14:00:00' (length=8)
  'time_stop' => null
  'load_panel' => string '1' (length=1)
 */
protected function action_new() {
    $this->show_form(null);
}

protected function action_detail($pk = null) {
    if (is_null($pk))
        $pk = $_POST['permission_id'];
    $stmt = $this->db->query('select * from permission
      where permission_id = :permission_id',
      array('permission_id' => $pk));
    if ($stmt->rowCount() == 0)
        $this->message('Failed to retrieve record.');
    $row = $stmt->fetch();
    $this->show_form($row);
}


protected function action_delete() {
    $stmt = $this->db->query('delete from permission where
      permission_id = :permission_id',
      array('permission_id' => $_REQUEST['pk']));
    if ($stmt->rowCount() == 1)
        $this->message('Deleted OK', true);
    else
        $this->message('Nothing deleted');
}

protected function action_save() {
    try {
        $pk = $this->db->update('permission', 'permission_id',
          array('permission'), $_POST);
    }
    catch (\Exception $e) {
        $this->show_form($_POST);
        throw $e;
    }
    $this->action_detail($pk);
    if (isset($exc))
        throw $exc;
    $this->message('Saved OK', true);
}

protected function show_form($row) {
    $f = new Form();
    $f->start($row);
    $f->hidden('permission_id', $row['permission_id']);
    $f->text('permission', 'Permission:', 30, 'permission');
    $f->button('action_save', 'Save');
    $f->end();
}

}

$page = new MyPage('Panels Report');
$page->go();

?>
