<?php
namespace EPMADD;
require_once 'lib/fpdf17/fpdf.php';
require_once 'lib/fpdf-plus/mc_table.php';
use \PDF_MC_Table;

define('PDF_MARGIN', 36);

class PDF_Report extends PDF_MC_Table {

protected $page_title, $page_width, $page_height, $headings;

function Header() {
    $this->SetX(-1); // negative arg means relative to right of page
    $this->page_width = $this->GetX() + 1;
    $this->SetY(-1); // negative arg means relative to bottom of page
    $this->page_height = $this->GetY() + 1;
    $this->SetFont('Helvetica', 'B', 10);
    $this->SetXY(0, PDF_MARGIN - 10);
    $this->MultiCell($this->page_width, 8, $this->page_title, 0, 'C');
    $this->SetY(PDF_MARGIN);
    $this->SetFont('Helvetica', 'I', 8);
    $this->RowX($this->headings, false);
}

function Footer() {
    $this->SetFont('Helvetica', 'I', 8);
    $y = $this->page_height - PDF_MARGIN / 2 - 8;
    $cell_width = $this->page_width - 2 * PDF_MARGIN;
    $this->SetXY(PDF_MARGIN, $y);
    $this->MultiCell($cell_width, 8, date('Y-m-d H:i:s'), 0, 'L');
    $this->SetXY(PDF_MARGIN, $y);
    $this->MultiCell($cell_width, 8, $this->PageNo() . ' of {nb}',
      0, 'R');
}

function set_headings($headings) {
    $this->headings = $headings;
}

function set_title($title) {
    $this->page_title = $title;
}

}

class Report {

function pdf($title, $stmt, $widths = null, $headings = null,
  $orientation = 'P', $pagesize = 'letter') {
    define('HORZ_PADDING', 2);
    define('VERT_PADDING', 3);
    $dir = 'output'; // previously created
    $path = "$dir/" . date('Y-m-d') . '-' . uniqid() . '.pdf';

    $url = "http://" . $_SERVER['HTTP_HOST'] .
      dirname($_SERVER['REQUEST_URI']) . "/$path";
    $pdf = new PDF_Report($orientation, 'pt', $pagesize);
    $pdf->set_title($title);
    $pdf->SetX(-1);
    $page_width = $pdf->GetX() + 1;
    $pdf->AliasNbPages();
    $pdf->SetFont('Helvetica', '' , 7);
    $pdf->SetLineWidth(.1);
    $pdf->SetMargins(PDF_MARGIN, PDF_MARGIN);
    $pdf->SetAutoPageBreak(true, PDF_MARGIN);
    $pdf->SetHorizontalPadding(HORZ_PADDING);
    $pdf->SetVerticalPadding(VERT_PADDING);
    $ncols = $stmt->columnCount();
    if (is_null($headings))
        for ($i = 0; $i < $ncols; $i++) {
            $meta = $stmt->getColumnMeta($i);
            $headings[] = $meta['name'];
        }
    $pdf->set_headings($headings);
    if (is_null($widths)) {
        $w = ($page_width - 2 * PDF_MARGIN) / $ncols;
        for ($i = 0; $i < $ncols; $i++)
            $widths[$i] = $w;
    }
    if (count($widths) == $ncols - 1) {
        $n = 0;
        foreach ($widths as $w)
            $n += $w;
        $widths[$ncols - 1] = $page_width - 2 * PDF_MARGIN - $n;
    }
    $pdf->SetWidths($widths);
    $pdf->AddPage();
    while ($row = $stmt->fetch()) {
        $r = array();
        foreach ($row as $v)
            $r[] = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $v);
        $pdf->RowX($r);
    }
    $pdf->Output($path, 'F');
    echo <<<EOT
    <p>Click below to access the report:
    <p><a href='$url'>$url</a>
EOT;
}

function html($title, $stmt, $headings = null) {
    $ncols = $stmt->columnCount();
    if (is_null($headings))
        for ($i = 0; $i < $ncols; $i++) {
            $meta = $stmt->getColumnMeta($i);
            $headings[] = $meta['name'];
        }
    echo "<p style='font-weight: bold;'>$title</p>";
    echo "<table border=1 cellpadding=5 cellspacing=0
      style='border-collapse: collapse;'>";
    echo "<tr>";
    foreach ($headings as $h)
        echo "<th>" . htmlspecial($h);
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        foreach ($row as $v)
            echo "<td>" . htmlspecial($v);
    }
    echo "</table>";
}

function csv($stmt, $convertUTF8 = false) {
    $dir = 'output'; // previously created
    $output_file = "$dir/" . date('Y-m-d') . '-' .
      uniqid() . '.csv';

    $output = fopen($output_file, "w");
    $ncols = $stmt->columnCount();
    for ($i = 0; $i < $ncols; $i++) {
        $meta = $stmt->getColumnMeta($i);
        $headings[] = $meta['name'];
    }
    $have_header = false;
    while ($row = $stmt->fetch()) {
        if (!$have_header) {
            fputcsv($output, array_keys($row));
            $have_header = true;
        }
        if ($convertUTF8) {
            $r = array();
            foreach ($row as $v)
                $r[] = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $v);
            fputcsv($output, $r);
        }
        else
            fputcsv($output, $row);
    }
    fclose($output);
    echo "<p>File to download:
      <a href='$output_file'><b>$output_file</b></a>";
    echo "<p>(Control-click or right-click and choose
      \"Save Link As...\", \"Download Linked File\",
      or equivalent.)";
}

}

?>
