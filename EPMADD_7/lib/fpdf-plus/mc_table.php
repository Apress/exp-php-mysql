<?php

class PDF_MC_Table extends FPDF
{
var $widths;
var $aligns;
var $styles; // added by MJR
var $vertical_padding = 0; // added by MJR

function SetWidths($w)
{
	//Set the array of column widths
	$this->widths=$w;
}

function SetAligns($a)
{
	//Set the array of column alignments
	$this->aligns=$a;
}

function SetStyles($styles_array) { // added by MJR
	$this->styles = $styles_array;
}

function SetVerticalPadding($vertical_padding) { // added by MJR
	$this->vertical_padding = $vertical_padding;
}

function GetVerticalPadding() { // added by MJR
	return $this->vertical_padding;
}

function SetHorizontalPadding($cMargin) { // added by MJR
	$this->cMargin = $cMargin;
}

function GetHorizontalPadding() { // added by MJR
	return $this->cMargin;
}

function Row($data) // Identical to "Table with MultiCells"
{                   // (Olivier, 2002-11-17) but reformatted
	//Calculate the height of the row
	$nb = 0;
	for ($i = 0; $i < count($data); $i++)
		$nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
	$h = 5 * $nb;
	//Issue a page break first if needed
	$this->CheckPageBreak($h);
	//Draw the cells of the row
	for ($i = 0; $i < count($data); $i++) {
		$w = $this->widths[$i];
		$a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
		//Save the current position
		$x = $this->GetX();
		$y = $this->GetY();
		//Draw the border
		$this->Rect($x, $y, $w, $h);
		//Print the text
		$this->MultiCell($w, 5, $data[$i], 0, $a);
		//Put the position to the right of the cell
		$this->SetXY($x + $w, $y);
	}
	//Go to the next line
	$this->Ln($h);
}

// added by MJR
function RowX($data, $want_rect = true)
{
	$nb = 0;
	for ($i = 0; $i < count($data); $i++) {
		if (isset($this->styles[$i]))
			$this->SetFont('', $this->styles[$i]);
		$nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
	}
	$h = $this->FontSize * $nb + 2 * $this->vertical_padding;
	$this->CheckPageBreak($h);
	$x = $this->GetX();
	$y = $this->GetY();
	for ($i = 0; $i < count($data); $i++) {
		$w = $this->widths[$i];
		$a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
		if (isset($this->styles[$i]))
			$this->SetFont('', $this->styles[$i]);
        if ($want_rect)
            $this->Rect($x, $y, $w, $h);
		$this->SetXY($x, $y + $this->vertical_padding);
		$this->MultiCell($w, $this->FontSize, $data[$i], 0, $a);
		$x += $w;
	}
	$this->SetY($y + $h);
}

// For TCPDF, comment out this function so the one in TCPDF will be used
function CheckPageBreak($h)
{
	//If the height h would cause an overflow, add a new page immediately
	if($this->GetY()+$h>$this->PageBreakTrigger)
		$this->AddPage($this->CurOrientation);
}

function NbLines($w,$txt)
{
	//Computes the number of lines a MultiCell of width w will take
	$cw=&$this->CurrentFont['cw'];
	if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
	$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	$s=str_replace("\r",'',$txt);
	$nb=strlen($s);
	if($nb>0 and $s[$nb-1]=="\n")
		$nb--;
	$sep=-1;
	$i=0;
	$j=0;
	$l=0;
	$nl=1;
	while($i<$nb)
	{
		$c=$s[$i];
		if($c=="\n")
		{
			$i++;
			$sep=-1;
			$j=$i;
			$l=0;
			$nl++;
			continue;
		}
		if($c==' ')
			$sep=$i;
		//$l+=$cw[ord($c)]; // for TCPDF
		$l+=$cw[$c]; // for FPDF
		if($l>$wmax)
		{
			if($sep==-1)
			{
				if($i==$j)
					$i++;
			}
			else
				$i=$sep+1;
			$sep=-1;
			$j=$i;
			$l=0;
			$nl++;
		}
		else
			$i++;
	}
	return $nl;
}
}
?>
