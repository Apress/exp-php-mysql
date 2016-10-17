<?php
namespace EPMADD;
/*
	Many of the code files for Chapter 5 (folder EPMADD_5) have better
	versions in EPMADD_6, EPMADD_7, or EPMADD_8. For your own
	applications, take the code from there rather than here.
*/
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
require_once('lib/common.php');
use PDOException;

class MyPage extends Page {
    
protected function request() {
	echo <<<EOT
<form action="{$_SERVER['PHP_SELF']}" method=post
  accept-charset=UTF-8>
<label for=userid>User ID:</label>
<input type=text size=50 name=userid id=userid
  placeholder='User ID'>
<label for=password>Password:</label>
<input type=password size=50 name=password id=password
  placeholder='Password'>
<br>
<input class=button type=submit name=pre_action_login value='Login'>
<input class=button type=submit name=action_forgot value='Forgot'>
</form>
EOT;
    if (isset($_REQUEST['msg']))
        $this->message($_REQUEST['msg']);
}

protected function pre_action_login() {
    // Somewhat naive!
    if ($_POST['password'] == 'cupcake') {
        $this->login($_POST['userid']);
        header('Location: member.php');
    }
    else
        header('Location: login.php?msg=Wrong%20password');
}

protected function pre_action_logout() {
    $this->logout();
    header('Location: login.php');
}

protected function action_forgot() {
    $this->message('Not yet implemented');
}

}

$page = new MyPage('Login', false);
$page->go();

?>
