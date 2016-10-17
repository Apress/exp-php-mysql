<?php
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
echo <<<EOT
<style>
label:before {
    content:"\a";
    white-space: pre;
}
</style>
EOT;
foreach ($_REQUEST as $name => $value)
    if (strpos($name, 'action_') === 0)
        $name();
echo <<<EOT
    <form action="{$_SERVER['PHP_SELF']}" method=post
      accept-charset=UTF-8>
    <label for=p1>p1:</label>
    <input type=text size=50 name=p1 id=p1>
    <label for=p2>p2:</label>
    <input type=text size=50 name=p2 id=p2>
    <br>
    <input type=submit name=action_button1 value='Click Me 1'>
    <input type=submit name=action_button2 value='Click Me 2'>
    </form>
    <button onclick='window.location="{$_SERVER['PHP_SELF']}\
?action_button3=1&p3=cake"'>
Click Me 3
</button>
EOT;
    
function action_button1() {
    echo <<<EOT
        Button 1 was clicked.
        <br>{$_REQUEST['p1']} -- {$_REQUEST['p2']}
EOT;
}
    
function action_button2() {
    echo <<<EOT
        Button 2 was clicked.
        <br>{$_REQUEST['p1']} -- {$_REQUEST['p2']}
EOT;
}
    
function action_button3() {
    echo <<<EOT
        Button 3 was clicked.
        <br>
EOT;
    print_r($_REQUEST);
}
