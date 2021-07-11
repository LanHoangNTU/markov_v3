<?php 
use MongoDB\Driver\BulkWrite;
use MongoDB\BSON\ObjectId;

require_once("../../configuration/db.php");

$id;
$response = null;

function isValidMatrix(string $matrix) {
    try {
        
        if (is_array(json_decode($matrix))) {
            $matrix = json_decode($matrix);
        } else {
            throw new Exception('Ma trận không hợp lệ 001');
        }
        if (count($matrix) != count($matrix[0])) {
            throw new Exception('Ma trận không hợp lệ 002');
        } 
        foreach ($matrix as $row) {
            $sum = 0;
            foreach ($row as $cell) {
                if (is_numeric($cell)) {
                    $cell = floatval($cell);
                    $sum += $cell;
                    if ($cell < 0 || $cell > 1) {
                        throw new Exception('Ma trận không hợp lệ 003');
                    }
                } else {
                    throw new Exception('Ma trận không hợp lệ 004');
                }
            }

            if ($sum != 1) {
                throw new Exception('Ma trận không hợp lệ 005');
            }
        }

        return array(
            'valid' => true,
            'message' => ''
        );
    } catch(Exception $e) {
        return array(
            'valid' => false,
            'message' => $e->getMessage()
        );
    }
}


if (isset($_POST['id'])) {
    $id = $_POST['id'];
    if (trim($id) == '') {
        $response = [
            'status' => 400,
            'body' => null,
            'message' => 'Id rỗng'
        ];
    }

    try {
        $id = new ObjectId($id);
    } catch (Exception $e) {
        $response = [
            'status' => 400,
            'body' => null,
            'message' => 'Id không hợp lệ'
        ];
    }
} else {
    $response = [
        'status' => 400,
        'body' => null,
        'message' => 'Id rỗng'
    ];
}

try {
    if ($response == null) {
        $db = Database::getInstance()->getDB();
        $bulk = new BulkWrite();
        if (isset($_POST['ma_tran'])) {
            $matrix = $_POST['ma_tran'];
            $check = isValidMatrix($matrix);
            if ($check['valid'] == true) {
                $bulk->update(
                    ['_id' => $id],
                    ['$set' => [
                        'ma_tran' => json_decode($matrix)
                    ]],
                    ['upsert' => true]
                );
    
                $result = $db->executeBulkWrite(Database::$status_matrix, $bulk);
                if ($result->getModifiedCount() <= 0) {
                    if ($result->getMatchedCount() <= 0) {
                        $response = [
                            'status' => 404,
                            'body' => null,
                            'message' => 'Không tìm thấy Id: '.$id
                        ];
                    } else {
                        $response = [
                            'status' => 400,
                            'body' => null,
                            'message' => 'Không thay đổi'
                        ];
                    }
                } else {
                    $response = [
                        'status' => 200,
                        'body' => null,
                        'message' => ''
                    ];
                }
            } else {
                $response = [
                    'status' => 400,
                    'body' => null,
                    'message' => $check['message']
                ];
            }
        }
    
        echo json_encode($response);
    } else {
        echo json_encode($response);
    }
} catch (Throwable $e) {
    echo (json_encode([
        'status' => 500,
        'body' => null,
        'message' => 'Internal server error'
    ]));
}
catch (Error $e) {
    echo (json_encode([
        'status' => 500,
        'body' => null,
        'message' => 'Internal server error'
    ]));
}

?>