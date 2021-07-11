<?php 

use MongoDB\Driver\BulkWrite;

require_once("../../configuration/db.php");
require_once("../../utils/unicode.php");

$students = (array) json_decode($_POST["students"], true);
$overwrite = $_POST["overwrite"];
$class = $_POST["class"];

$db = Database::getInstance()->getDB();
$bulk = new BulkWrite();
$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
if ($overwrite == "true" || $overwrite == true) {
	$bulk->delete(["lop" => $class], ['limit' => 0]);
}

foreach ($students as $student) {
	// $student["lop"] = floatval($student["lop"]);
	$temp = array();
	$date = explode("/", $student['ngay_sinh']);
	$student['ngay_sinh'] = new MongoDB\BSON\UTCDateTime(new DateTime(implode("-", array($date[2], $date[1], $date[0]))));
	foreach ($student['mon_hoc'] as $key => $value) {
		$temp[] = array('key' => $key, 'score' => floatval($value));
	}
	$student['mon_hoc'] = $temp;
	unset($student['nang_khieu']);
	$bulk->insert($student);
}

$result = $db->executeBulkWrite(Database::$students, $bulk, $writeConcern);
echo json_encode(array("allow_overwrite" => $overwrite, "response" => $result));
?>