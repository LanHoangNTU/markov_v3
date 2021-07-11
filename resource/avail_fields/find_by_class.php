<?php

use MongoDB\BSON\ObjectId;
use MongoDB\Driver\Exception\InvalidArgumentException;
use MongoDB\Driver\Query;

require_once("../../configuration/db.php");
require_once("../../utils/unicode.php");

$db = Database::getInstance()->getDB();
$lop;
if (!isset($_POST['lop'])) {
    echo json_encode(["status" => 400, "message" => "Lớp rỗng"]);
    return;
} else {
    $lop = $_POST['lop'];
}

// Find one query
$query = new Query(["lop" => $lop]);

// Execute query in collection "available_fields"
$avail_fields = $db->executeQuery(Database::$avail_fields, $query)->toArray();
if (count($avail_fields) > 1 || count($avail_fields) <= 0) {
    echo json_encode(["status" => 404, "message" => "Không tìm thấy lớp ".$class]);
    exit;
} else {
    $data = $avail_fields[0];
    echo json_encode(["status" => 200, "data" => $data]);
}
?>