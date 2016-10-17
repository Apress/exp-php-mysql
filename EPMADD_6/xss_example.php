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
require_once 'lib/common.php';
use PDOException;

class MyPage extends Page {
    
protected function request() {
    $val = isset($_POST['field']) ? $_POST['field'] : '';
    echo <<<EOT
    <form action='{$_SERVER['PHP_SELF']}' method=post
      accept-charset=UTF-8>
    <input type=text name=field size=115 value='$val'>
    <input type=submit name=action_go value=Submit>
    </form>
<!--
<button type=button onclick='window.location=
  "member.php?csrftoken={$_SESSION['csrftoken']}";'>Go</button>
<button type=button onclick="transfer('member.php',
  {csrftoken: '{$_SESSION['csrftoken']}'});">Go</button>
-->
EOT;
}

protected function action_go() {
    // ... code to save data ...
    $this->message('Saved', true);
}

}

$page = new MyPage('XSS Example', true);
$page->go();

?>
