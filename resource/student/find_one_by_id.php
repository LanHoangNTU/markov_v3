<?php

use MongoDB\BSON\ObjectId;
use MongoDB\Driver\Query;

require_once("../../configuration/db.php");
require_once("../../utils/unicode.php");

$db = Database::getInstance()->getDB();

// Find all query
if (isset($_POST["id"]) || !empty($_POST["id"])) {
    $_id = $_POST["id"];
    $query = new Query(["_id" => new ObjectId($_id)]);
    // Execute query in collection "students"
    $student = $db->executeQuery(Database::$students, $query)->toArray();
    if (count($student) < 1) {
        echo json_encode([
            "status" => 404,
            "Not found"
        ]);
    } else {
        $student[0]->ngay_sinh = $student[0]->ngay_sinh->toDateTime();
        echo json_encode($student[0]);
    }
} else {
    echo json_encode([
        "status" => 400,
        "Bad request"
    ]);
}


?>