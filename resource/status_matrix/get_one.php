<?php 

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Query;

require_once("../../configuration/db.php");

function isValidStatusMatrix($statusMatrix, $availableFields) {
    $size = count($availableFields->mon_hoc);
    if (count($statusMatrix->ma_tran) != $size) {
        return false;
    }

    foreach ($statusMatrix->ma_tran as $value) {
        if (count($value) != $size) {
            return false;
        }
    }

    return true;
}

$class = $_POST['class'];

$db = Database::getInstance()->getDB();
$query = new Query(['lop' => $class]);
$statusMatrix = $db->executeQuery(Database::$status_matrix, $query)->toArray();
$availableFields = $db->executeQuery(Database::$avail_fields, $query)->toArray();

if (count($availableFields) > 0) {
    $availableFields = $availableFields[0];
    if (count($statusMatrix) <= 0) {
        $size = count($availableFields->mon_hoc);
        $array = array_fill(0, $size, array_fill(0, $size, (1.0 / $size)));
        $result = array('lop' => $class, 'ma_tran' => $array);
        $insert = new BulkWrite();
        $insert->insert($result);
        $db->executeBulkWrite(Database::$status_matrix, $insert);
        $result['mon_hoc'] = $availableFields->mon_hoc;
        echo json_encode(array(
            'status' => 201, 
            'body' => $result, 
            'message' => 'Tạo mới ma trận chuyển đổi trạng thái lớp'.$class
        ));
    } else {
        $statusMatrix = $statusMatrix[0];
        if (!isValidStatusMatrix($statusMatrix, $availableFields)) {
            echo json_encode(array(
                'status' => 500, 
                'body' => null, 
                'message' => 'Ma trận chuyển đổi trạng thái lớp '.$class.' thừa hoặc thiếu môn học'
            ));
        } else {
            $result = array(
                'lop' => $class, 
                'ma_tran' => $statusMatrix, 
                'mon_hoc' => $availableFields->mon_hoc
            );

            echo json_encode(array(
                'status' => 200,
                'body' => $result, 
                'message' => 'Ma trận chuyển đổi trạng thái lớp '.$class
            ));
        }
    }
} else {
    echo json_encode(array(
        'status' => 404, 
        'body' => null, 
        'message' => 'Không tồn tại thông tin các môn học của lớp'.$class
    ));
}

?>