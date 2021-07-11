<?php

use MongoDB\BSON\ObjectId;
use MongoDB\Driver\Exception\InvalidArgumentException;
use MongoDB\Driver\Query;

require_once("../../configuration/db.php");
require_once("../../utils/unicode.php");

$db = Database::getInstance()->getDB();
$class = ( isset($_POST["lop"]) || !empty($_POST["lop"]) )
         ? $_POST["lop"]
         : null;

$student_id = ( isset($_POST["student_id"]) || !empty($_POST["student_id"]) )
         ? $_POST["student_id"]
         : null; 

// Find one query
$query = null;

if ($student_id != null) {
    try {
        $query = new Query(
            ["_id" => new ObjectId($student_id)], 
            [
                'projection' => [
                    'lop' => 1
                ],
                'limit' => 1
            ]
        );
        $student = $db->executeQuery(Database::$students, $query)->toArray();

        if (count($student) < 1) {
            echo json_encode(["status" => 404, "message" => "Không tìm thấy học sinh"]);
            exit;
        } else {
            $student = $student[0];
            $query = new Query(["lop" => $student->lop]);
        }
    } catch (InvalidArgumentException $e) {
        echo json_encode(["status" => 400, "message" => $e->getMessage(), "query" => $query]);
        exit;
    }
} else if ($class != null) {
    $query = new Query(["lop" => $class]);
} else {
    $query = new Query([]);
}

// Execute query in collection "available_fields"
$avail_fields = $db->executeQuery(Database::$avail_fields, $query)->toArray();
if (count($avail_fields) <= 0) {
    echo json_encode(["status" => 404, "message" => "Không tìm thấy lớp ".$class]);
} else if (count($avail_fields) > 1) { 
    echo json_encode(["status" => 400, "message" => "Lớp ".$class." không hợp lệ"]);
} else {
    $data = $avail_fields[0];
    echo json_encode(["status" => 200, "data" => $data]);
}
?>