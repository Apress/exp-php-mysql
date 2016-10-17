<?php
namespace EPMADD;
require_once 'DbAccess.php';

class Access {

protected $db;

function __construct($db) {
    $this->db = $db;
}

function load_permissions() {
    if (isset($_SESSION)) {
        $_SESSION['permissions'] = array();
        $stmt = $this->db->query('select permission from
          user_role join role_permission using (role)
          where userid = :userid',
          array('userid' => $_SESSION['userid']));
        while ($row = $stmt->fetch())
            $_SESSION['permissions'][$row['permission']] = 1;
    }
}

function show_permissions($userid) {
    $stmt = $this->db->query('select role, permission from
      user_role join role_permission using (role)
      where userid = :userid order by role, permission',
      array('userid' => $userid));
    echo "<table border=1 cellpadding=4 style='border-collapse:collapse;'>";
    echo '<tr><th>Role<th>Permission';
    while ($row = $stmt->fetch())
        echo "<tr><td>{$row['role']}<td>{$row['permission']}";
    echo '</table>';
}

function check_permissions($permissions) {
    if (isset($_SESSION['permissions']['admin']))
        return;
    if (isset($permissions)) {
        if (!is_array($permissions))
            $permissions = array($permissions);
        foreach ($permissions as $p)
            if (empty($_SESSION['permissions'][$p]))
                throw new \Exception("You don't have permission
                  to access this page");
    }
}

function has_permission($permission) {
    return isset($_SESSION['permissions']['admin']) ||
      isset($_SESSION['permissions'][$permission]);
}

function get_permissions() {
    $stmt = $this->db->query('select permission from permission
      order by permission');
    $a = array();
    while ($row = $stmt->fetch())
        $a[] = $row['permission'];
    return $a;
}

}

?>
