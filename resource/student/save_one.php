<?php 
use MongoDB\Driver\BulkWrite;
use MongoDB\BSON\ObjectId;

require_once("../../configuration/db.php");

$student = json_decode(file_get_contents('php://input'));
$id;
if (empty($student->id)) {
    $id = '';
} else {
    $id = $student->id;
}
$date = explode("-", $student->ngay_sinh);
// $student->lop = intval($student->lop);
$student->ngay_sinh = new MongoDB\BSON\UTCDateTime(new DateTime(implode("-", array($date[2], $date[1], $date[0]))));
$db = Database::getInstance()->getDB();
$bulk = new BulkWrite();

unset($student->id);
if ($id == '') {
    $id = new ObjectId();
} else {
    $id = new ObjectId($id);
}

$bulk->update(['_id' => $id], ['$set' => $student], ['upsert' => true]);
$student->id = $id;
$result = $db->executeBulkWrite(Database::$students, $bulk);
echo json_encode(array(
    'ids' => json_encode($result->getUpsertedIds()),
    'inserts' => $result->getInsertedCount(),
    'updates' => $result->getModifiedCount(),
    'upserts' => $result->getUpsertedCount(),
    'data' => $student
));
?>