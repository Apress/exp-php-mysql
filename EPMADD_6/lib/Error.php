<?php
namespace EPMADD;

class Error {

function log($s) {
    if (is_array($s))
        error_log(print_r($s, true));
    else {
        if (is_a($s, 'Exception'))
            $s = $s->getMessage();
        error_log($s);
    }
}

}

function log($s) {
    static $error;
    
    if (is_null($error))
        $error = new Error();
    $error->log($s);
}

?>
