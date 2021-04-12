<?php

use MongoDB\Driver\Query;

require_once("../../configuration/db.php");
require_once("../../utils/unicode.php");

$db = Database::getInstance()->getDB();

// Find all query
$query = ( isset($_POST["class"]) || !empty($_POST["class"])) 
         ? new Query(["class" => intval($_POST["class"])]) 
         : new Query([]);
// Execute query in collection "students"
$students = $db->executeQuery(Database::$students, $query);
$student_array = $students->toArray();
$key_header_array = array();
foreach ($student_array as $elem) {
    foreach ($elem->subjects as $subj) {
        array_push($key_header_array, $subj->key);
    }

    break;
}
$key_header_array = array_unique($key_header_array);
$data = array("headers" => $key_header_array, "students" => $student_array);
echo json_encode($data);

?>