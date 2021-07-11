<?php

use MongoDB\Driver\Query;

require_once("../../configuration/db.php");
require_once("../../utils/unicode.php");

$db = Database::getInstance()->getDB();

$query = new Query([], ['projection' => ['_id'=> 0]]);

$field_mapping = $db->executeQuery(Database::$field_mapping, $query)->toArray();
echo json_encode($field_mapping);

?>