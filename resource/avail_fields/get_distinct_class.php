<?php 
use MongoDB\Driver\Command;

require_once("../../configuration/db.php");
$db = Database::getInstance()->getDB();

$command = new Command([
    'distinct' => 'available_fields',
    'key' => 'lop'
]);

$cursor = $db->executeCommand('markov', $command);
echo json_encode($cursor->toArray());
?>