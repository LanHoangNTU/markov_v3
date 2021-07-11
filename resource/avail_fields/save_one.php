<?php 
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Query;
use MongoDB\BSON\ObjectId;

require_once("../../configuration/db.php");

$id;
$isUpdate = false;
if (isset($_POST['id']) && trim($_POST['id']) != '') {
    $id = new ObjectId($_POST['id']);
    $isUpdate = true;
} else {
    $id = new ObjectId();
}

if (isset($_POST['mon_hoc']) && isset($_POST['lop'])) {
    if (!is_array(json_decode($_POST['mon_hoc']))) {
        echo json_encode([
            'status' => 400,
            'body' => null,
            'message' => 'Dữ liệu không hợp lệ 001'
        ]);
        return;
    }
    $lop = $_POST['lop'];
    $monHoc = json_decode($_POST['mon_hoc']);

    if (count($monHoc) <= 0 || trim($lop) == '') {
        echo json_encode([
            'status' => 400,
            'body' => null,
            'message' => 'Dữ liệu không hợp lệ 002'
        ]);
        return;
    }

    $db = Database::getInstance()->getDB();
    $bulk = new BulkWrite();
    $query = new Query(['lop' => $lop]);
    if ($isUpdate == false) {
        $search = $db->executeQuery(Database::$avail_fields, $query)->toArray();
        if (count($search) != 0) {
            echo json_encode([
                'status' => 409,
                'body' => null,
                'message' => 'Lớp '.$lop.' đã tồn tại'
            ]);
            return;
        }
    } 

    $entity = [
        '_id' => $id,
        'lop' => $lop,
        'mon_hoc' => $monHoc
    ];

    $bulk->update(
        ['_id' => $id],
        ['$set' => $entity],
        ['upsert' => true]
    );

    $result = $db->executeBulkWrite(Database::$avail_fields, $bulk);
    if ($result->getInsertedCount() > 0 || $result->getModifiedCount() > 0 || $result->getUpsertedCount() > 0) {
        if ($isUpdate == true) {
            $size = count($monHoc);
            $array = array_fill(0, $size, array_fill(0, $size, (1.0 / $size)));
            $result = array('ma_tran' => $array);
            $mtrixInsert = new BulkWrite();
            $mtrixInsert->update(['lop' => $lop], ['$set' => $result]);
            $db->executeBulkWrite(Database::$status_matrix, $mtrixInsert);
        }
        echo json_encode([
            'status' => 200,
            'body' => $id,
            'message' => 'Cập nhật thành công'
        ]);
        return;
    } else {
        echo json_encode([
            'status' => 400,
            'body' => null,
            'message' => 'Dữ liệu không hợp lệ 003'
        ]);
        return;
    }
} 

?>