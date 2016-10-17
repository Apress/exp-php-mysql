<?php
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

// $path = "/Users/marc/Sites/cwadb/pastdata/List-Table 1.csv";
// $in = fopen($path, "r") or die("can't open $path");
// if ($a = fgetcsv($in)) {
// 	$k = 0;
// 	foreach ($a as $f) {
// 		$colname[$k] = $f;
// 		echo "<br>{$colname[$k]}";
// 		$k++;
// 	}
// }
// fclose($in);
// 
// echo "<hr>";
// exit;

$path = "/Users/marc/Sites/cwadb/pastdata/Participant 2007-UTF8.csv";
$in = fopen($path, "r") or die("can't open $path");
if ($a = fgetcsv($in)) {
	$k = 0;
	foreach ($a as $f) {
		$colname[$k] = $f;
		echo "<br>{$colname[$k]}";
		$k++;
	}
}
fclose($in);

echo "<hr>";


$path = "/Users/marc/Sites/cwadb/pastdata/Participant 2007-UTF8.csv";
$in = fopen($path, "r") or die("can't open $path");
if ($a = fgetcsv($in)) {
	$k = 0;
	foreach ($a as $f) {
		$colname[$k] = $f;
		echo "<br>\$row[''] = \$data['{$colname[$k]}']";
		$k++;
	}
}
fclose($in);


$path = "/Users/marc/Sites/cwadb/pastdata/Participant 2007-UTF8.csv";
$in = fopen($path, "r") or die("can't open $path");
if ($a = fgetcsv($in)) {
	$k = 0;
	foreach ($a as $f) {
		$colname[$k] = $f;
		//echo "<br>\$row[''] = \$data['{$colname[$k]}']";
		$k++;
	}
}
while ($a = fgetcsv($in)) { // for lines 2 and beyond
		$k = 0;
		foreach ($a as $v)
			$data[$colname[$k++]] = trim($v);
		$row = array();
		$row['name_first'] = $data['Name_First'];
		$row['name_last'] = $data['Name_Last'];
		$row['appellation'] = $data['Appellation'];
		$row['home_street1'] = $data['Home Address'];
		$row['home_city'] = $data['Home City'];
		$row['conversion_data'] = conversion_data($row, basename($path));
		//...
		//update_database('person', 'person_pk', array(...), $row);
		//echo "<pre>";var_dump($row);echo "</pre>";
		echo "<hr><pre>{$row['conversion_data']}</pre>";
}
fclose($in);

function conversion_data($row, $label) {
	$s = "$label\n\n";
	foreach ($row as $k => $v)
		if (!empty($v))
			$s .= "$k: $v\n";
	return $s;
}

exit;

// htmlentities($s, ENT_QUOTES, 'UTF-8');

$nf = -1;
$line = 0;
while ($a = fgetcsv($in)) {
	$n++;
	if ($line == 1) {
		$k = 0;
		foreach ($a as $f) {
			if (empty($f))
				$f = "col" . ($k + 1);
			$colname[$k] = str_replace(' ', '_', $f);
			echo "<br>{$colname[$k]}";
			$k++;
		}
	}
	else {
		//echo "<tr>";
		$k = 0;
		foreach ($a as $v) {
			//echo "<td>$v";
			//$v = utf8_encode($v);
			$v = str_replace("’", "'", $v);
			$v = str_replace("–", "--", $v);
			$row[$colname[$k++]] = trim($v);
		}
		//process_panel($n, $row);
		if ($nf == -1)
			$nf = count($a);
		else if ($nf != count($a)) {
			die("<p class=msg>****** wrong number of columns");
		}
	}
	if ($n > 10)
		break;
}

// 		echo "<br>$f --> ";
// 		$f = str_replace('&', '_and_', $f);
// 		$f = str_replace('#', '_num_', $f);
// 		$f = str_replace('/', '_or_', $f);
// 		$f = preg_replace('/[^a-zA-Z0-9_]/', '_', $f);
// 		$f = preg_replace('/__*$/', '', $f);
// 		$f = preg_replace('/^__*/', '', $f);
// 		$colname[$k] = preg_replace('/__+/', '_', $f);

?>
