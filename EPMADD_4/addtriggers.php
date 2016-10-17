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
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'mydb');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '...');
try {
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT .
      ';dbname=' . DB_NAME;
    $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE,
      PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,
      PDO::FETCH_ASSOC);
    add_triggers($pdo, 'manager', "
    if office not in
    ('DALLAS', 'BOSTON', 'PARIS', 'TOKYO') then
        signal SQLSTATE value 'CK001'
        set MESSAGE_TEXT = 'Invalid OFFICE value.';
    end if;
    ");
$pdo->beginTransaction();
// ... several SQL statements ...
$pdo->commit();
}
catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction())
        $pdo->rollBack();
    die(htmlspecialchars($e->getMessage()));
}

function add_triggers($pdo, $table, $sql) {
    $stmt = $pdo->prepare('select column_name, column_type
      from information_schema.columns
      where table_schema = :dbname and table_name = :table');
    $stmt->execute(array('dbname' => DB_NAME, 'table' => $table));
    $cols = $parms = '';
    while ($row = $stmt->fetch()) {
        $cols .= ", new.{$row['column_name']}";
        $parms .= ", {$row['column_name']} {$row['column_type']}";
    }
    $cols = substr($cols, 2); // extra ", " at front
    $parms = substr($parms, 2);
    echo "<p>$cols";
    echo "<p>$parms";
    $trigger1_name = "table_{$table}_trigger1";
    $trigger2_name = "table_{$table}_trigger2";
    $proc_name = "check_table_{$table}";
    $trigger1_create = "create trigger $trigger1_name
      before insert on $table for each row begin
      call check_table_{$table}($cols); end";
    $trigger2_create = "create trigger $trigger2_name
      before update on $table for each row begin
      call check_table_{$table}($cols); end";
    $proc_create = "create procedure $proc_name($parms)
      begin $sql end";
    echo "<p>$trigger1_create";
    echo "<p>$trigger2_create";
    echo "<p>$proc_create";
    $pdo->exec("drop procedure if exists $proc_name");
    $pdo->exec("drop trigger if exists $trigger1_name");
    $pdo->exec("drop trigger if exists $trigger2_name");
    $pdo->exec($trigger1_create);
    $pdo->exec($trigger2_create);
    $pdo->exec($proc_create);
    echo "<p>Success!";
}
?>