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
require_once('markdown.php');

define("DB_USER", "rochkind_hesk");
define("DB_PASSWORD", "...");

$pdo = new PDO('mysql:host=localhost;dbname=rochkind_hesk',
  DB_USER, DB_PASSWORD);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$s = Markdown(file_get_contents("CWA-requirements.txt"));
while (preg_match('/^(.*)\{(\w+) (\d+)}(.*)$/s', $s, $m))
	$s = $m[1] . issue($m[2], $m[3]) . $m[4];
echo $s;

function issue($cmd, $n) {
	global $pdo;

	$stmt = $pdo->prepare("select * from hesk_tickets where id = :id");
	$stmt->execute(array('id' => $n));
	if ($row = $stmt->fetch()) {
		if ($cmd == "Issue")
			return "
				<table border=1 cellspacing=0 cellpadding=10><tr><td>
				<p><b>Issue {$row['id']}: {$row['subject']}</b>
				<p>{$row['message']}
				</table>
			  ";
		else	
			return "<br>Issue {$row['id']}: {$row['subject']}";
	}
	else
		return "<b>[Can't locate Issue $n]</b>";
}
?>
