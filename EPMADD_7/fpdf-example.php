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
use \FPDF;
use \PDO;
use \PDF_MC_Table;

f5();

function f1() {

$pdf = new FPDF('L', 'pt', 'legal');

$pdf->SetFont('Times', '', 50);
$pdf->AddPage();
$pdf->Text(100, 200, 'Hello World!');
$pdf->Output();

}

function f2() {
$pdf = new FPDF('P', 'pt', array(5 * 72, 6 * 72));
$pdf->AddPage();
$pdf->SetLineWidth(2);
$pdf->SetDrawColor(50, 50, 50);
$pdf->SetFillColor(220);
$pdf->Rect(50, 150, 100, 100, 'DF');
$pdf->SetLineWidth(6);
$pdf->SetDrawColor(190);
$pdf->Line(30, 30, 300, 400);
$pdf->Image('incl/logo.png', 60, 160);
$pdf->Output();
}

function setup_page($pdf, &$margin_left, &$margin_top,
  &$height, &$width) {
    $pdf->AddPage();
    $pdf->SetX(-1);
    $width = $pdf->GetX() + 1;
    $pdf->SetY(-1);
    $height = $pdf->GetY() + 1;

    $pdf->SetFillColor(220);
    $pdf->Rect(0, 0, $width, $height, 'F');
    $inset = 18;
    $pdf->SetLineWidth(6);
    $pdf->SetDrawColor(190);
    $pdf->SetFillColor(255);
    $pdf->Rect($inset, $inset, $width - 2 * $inset,
      $height - 2 * $inset, 'DF');

    $margin_left = $inset + 20;
    $margin_top = $inset + 20;
    $pdf->Image('incl/logo.png', $margin_left, $margin_top);
    $x = $margin_left + 50;
    $pdf->SetFont('Helvetica', 'BI', 16);
    $pdf->SetTextColor(100);
    $pdf->Text($x, $margin_top + 20,
      'Front Range Butterfly Club');
    $pdf->SetFont('Helvetica', 'I', 9);
    $pdf->SetTextColor(180);
    $pdf->Text($x, $margin_top + 32,
      '220 S. Main St., Anytown, CA 91234, 800-555-1234');
    $pdf->SetLineWidth(1);
    $pdf->Line($margin_left, $margin_top + 45,
      $width - $margin_left, $margin_top + 45);
    $pdf->SetFont('Times', '', 10);
    $pdf->SetTextColor(0);
}

function f3() {

$db = new DbAccess();
$pdo = $db->getPDO();
$pdf = new FPDF('P', 'pt', array(5 * 72, 6 * 72));
$stmt = $pdo->query('select * from member
  order by last, first limit 4');
while ($row = $stmt->fetch()) {
    setup_page($pdf, $margin_left, $margin_top, $height, $width);
    $pdf->Text($margin_left, $margin_top + 80,
      "[letter to {$row['first']} {$row['last']}]");
}
$pdf->Output();
}

function f4() {
    
$body = <<<EOT
If you haven't heard, our Spring 2013 Meadow Adventure is scheduled for Saturday, June 22. We'll meet at the Caribou Ranch trailhead, about 2 miles north of Nederland (make a sharp left at CR 126). Make sure you're ready to go at 9 AM. Bring the usual gear, and don't forget rainwear.
    
See you on the 22nd!

Regards,
Tom Swallowtail,
FRBC Event Coordinator
EOT;

$db = new DbAccess();
$pdo = $db->getPDO();
$pdf = new FPDF('P', 'pt', array(5 * 72, 6 * 72));
$stmt = $pdo->query('select * from member
  order by last, first limit 2');
while ($row = $stmt->fetch()) {
    $text = date('F j, Y') .
      "\n\nDear {$row['first']} {$row['last']}:\n\n$body";
    setup_page($pdf, $margin_left, $margin_top, $height, $width);
    $pdf->SetXY($margin_left, $margin_top + 80);
    $pdf->MultiCell($width - 2 * $margin_left, 12, $text, 0, 'L');
}
$pdf->Output();
}

function f5() {

$db = new DbAccess();
$pdo = $db->getPDO();
$pdf = new PDF_MC_Table('P', 'pt', 'letter');
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetWidths(array(72, 72, 100, 72, 36));
$pdf->SetVerticalPadding(5);
$pdf->AddPage();
$stmt = $pdo->query('select last, first, street, city, state
  from member order by last, first limit 50');
while ($row = $stmt->fetch(PDO::FETCH_NUM))
    $pdf->RowX($row);
$pdf->Output();
}
?>
