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

date_default_timezone_set("America/Denver");

test('01-02-03');
test('01-02-88');
test('02-Jan-03');
test('02-Jan-88');
test('January 2, 1988');

function test($s) {
	echo "<br>$s --> " . convert_date($s);
}

function convert_date($s) {
	if (empty($s))
		return null;
	if (preg_match("~^(\d{1,2})[-/.](\d{1,2})[-/.](\d{1,2})$~", trim($s), $m)) {
		$y = $m[3] < 48 ? 2000 + $m[3] : 1900 + $m[3];
		return "$y-{$m[1]}-{$m[2]}";
	}
	if (preg_match("~^(\d{1,2})[-/.]([A-Za-z]+)[-/.](\d{1,2})$~", trim($s), $m)) {
		$y = $m[3] < 48 ? 2000 + $m[3] : 1900 + $m[3];
		$month = date('m', strtotime($m[2]));
		return "$y-$month-{$m[1]}";
	}
	return date("Y-m-d", strtotime($s)); // can handle above, but not well defined
}

?>
