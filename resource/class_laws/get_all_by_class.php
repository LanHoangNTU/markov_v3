<?php

use MongoDB\Driver\Query;

require_once("../../configuration/db.php");
require_once("../../utils/unicode.php");

$db = Database::getInstance()->getDB();

$class = $_POST['class'];

$query = new Query(['lop' => $class], ['projection' => ['_id'=> 0]]);

$class_laws = $db->executeQuery(Database::$class_laws, $query)->toArray();
echo json_encode($class_laws);

?>