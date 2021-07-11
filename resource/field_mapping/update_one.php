<?php 
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Query;
use MongoDB\BSON\ObjectId;

require_once("../../configuration/db.php");

$response;
$tag;
$dinhDanh;
try {
    if (isset($_POST['tag']) && isset($_POST['dinh_danh'])) {
        $tag = trim($_POST['tag']);
        $dinhDanh = trim($_POST['dinh_danh']);
        if (empty($tag) || empty($dinhDanh)) {
            throw new Exception('Không hợp lệ', 400);
        }
    } else {
        throw new Exception('Không hợp lệ', 400);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => $e->getCode(),
        'message' => $e->getMessage()
    ]);
    return;
}

$db = Database::getInstance()->getDB();
$query = new Query(['tag' => $tag]);
$bulk = new BulkWrite();
$existed = $db->executeQuery(Database::$field_mapping, $query)->toArray();
if (count($existed) == 0) {
    echo json_encode([
        'status' => 404,
        'message' => 'Tag "'.$tag.'" không tồn tại'
    ]);
} else {
    $bulk->update(
        ['tag' => $tag],
        [
            '$set' => ['dinh_danh' => $dinhDanh]
        ],
        ['upsert' => false]
    );

    $db->executeBulkWrite(Database::$field_mapping, $bulk);
    echo json_encode([
        'status' => 200,
        'message' => 'Lưu thành công'
    ]);
}

?>