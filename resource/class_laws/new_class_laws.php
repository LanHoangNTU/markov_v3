<?php 
use MongoDB\Driver\BulkWrite;

require_once("../../configuration/db.php");
require_once("../../utils/unicode.php");

function validateClassLaw($class, $laws) {
    try {
        intval($class);
        if (count($laws) <= 0) {
            throw new Exception("Invalid class laws");
        }
        foreach ($laws as $value) {
            doubleval($value['score']);
        }

        return true;
    } catch (Exception $e) {
        return false;
    }
}

$classLaws = json_decode($_POST["bo_luat"], true);
// $class = floatval($_POST["lop"]);
$nk = $_POST["nang_khieu"];

$db = Database::getInstance()->getDB();
$bulk = new BulkWrite();
$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);

$bulk->insert(array('lop' => $class, 'nang_khieu' => $nk, 'bo_luat' => $classLaws));

$result = $db->executeBulkWrite(Database::$class_laws, $bulk, $writeConcern);
echo json_encode(array("response" => $result));

 ?>