<?php

use MongoDB\Driver\Query;

require_once("../../configuration/db.php");
require_once("../../utils/unicode.php");
require_once("../../domain/matrix.php");

function restructureArray ($scores, $fields)
{
    $array = array();
    foreach ($fields as $value) {
        $array[$value] = $scores[$value];
    }

    return $array;
}

$scores = json_decode($_POST["scores"], true);
$n = intval($_POST["n"]);

$db = Database::getInstance()->getDB();
$query = new Query(["lop" => $_POST['class']]);
$status_matrix = $db->executeQuery(Database::$status_matrix, $query)->toArray();
$fields = $db->executeQuery(Database::$avail_fields, $query)->toArray()[0]->mon_hoc;
$count = count($status_matrix);
$scores = restructureArray($scores, $fields);
if ($count == 1) {
    try {
        $result = (array) $status_matrix[0]->ma_tran;
        $matrix = new Matrix(count($result), count($result[0]));
        $matrix->setArray($result);

        $score_matrix = new Matrix(1, $matrix->getRowLength());
        $result = array();

        $score_matrix->set(array_values($scores));

        $result["n0"] = $scores;
        for ($i=1; $i <= $n; $i++) { 
            $score_matrix = Matrix::multiply($score_matrix, $matrix);
            $index = 0;
            foreach ($scores as $key => $value) {
                $scores[$key] = round($score_matrix->getAt(0, $index), 2);
                $index += 1;
            }

            $result[("n".$i)] = $scores;
        }

        echo json_encode(["array" => $matrix->getArray(), "matrix" => $result]);
    } catch (Exception $e) {
        echo json_encode(["status" => 503, "message" => $e->getMessage()]);
    } catch (TypeError $e) {
        echo json_encode(["status" => 503, "message" => $e->getMessage()]);
    }
    
} else if ($count > 1) {
    echo json_encode([
        "status" => 503,
        "message" => "Internal server error"
    ]);
    
} else {
    echo json_encode([
        "status" => 404,
        "message" => "Not found"
    ]);
}
?>