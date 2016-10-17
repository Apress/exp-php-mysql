<?php
namespace EPMADD;
use PDO;

class DbAccess {

function getPDO() {
    static $pdo;

    if (!isset($pdo)) {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT .
          ';dbname=' . DB_NAME;
        $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,
          PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,
          PDO::FETCH_ASSOC);
        $pdo->exec('set session sql_mode = traditional');
        $pdo->exec('set session innodb_strict_mode = on');
    }
    return $pdo;
}

// do not include $pkfield in $fields
function update($table, $pkfield, $fields, $data,
  &$row_count = null) {
    $input_parameters = array();
    $upd = '';
    foreach ($fields as $f) {
        if (!isset($data[$f]) || is_null($data[$f]))
            $v = 'NULL';
        else {
            $v = ":$f";
            $input_parameters[$f] = $data[$f];
        }
        $upd .= ", $f=$v";
    }
    $upd = substr($upd, 2);
    if (empty($data[$pkfield]))
        $sql = "insert $table set $upd";
    else {
        $input_parameters[$pkfield] = $data[$pkfield];
        $sql = "update $table set $upd
          where $pkfield = :$pkfield";
    }
    $stmt = $this->query($sql, $input_parameters, $insert_id);
    $row_count = $stmt->rowCount();
    return $insert_id;
}

function query($sql, $input_parameters = null, &$insert_id = null) {
    $pdo = $this->getPDO();
    $insert_id = null;
    if (is_null($input_parameters))
        $stmt = $pdo->query($sql);
    else {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($input_parameters);
    }
    if (stripos($sql, 'insert ') === 0)
        $insert_id = $pdo->lastInsertId();
    return $stmt;
}

}

?>
