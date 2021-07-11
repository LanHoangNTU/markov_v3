<?php

use MongoDB\Driver\Query;

require_once("../../configuration/db.php");
require_once("../../utils/unicode.php");
require_once("../../domain/matrix.php");

class Condition {
	public $subject;
	public $comparator;
	public $score;
	const GT = ">";
	const LT = "<";
	const EGT = ">=";
	const ELT = "<=";
	const EQ = "=";

	public function __construct($subject, $comparator, $score)
	{
		$this->subject = $subject;
		$this->comparator = $comparator;
		$this->score = $score;
	}

	public function checkCondition($my_score){
		switch ($this->comparator) {
			case self::GT:
				return ($my_score > $this->score);
				break;

			case self::EGT:
				return ($my_score >= $this->score);
				break;

			case self::LT:
				return ($my_score < $this->score);
				break;

			case self::ELT:
				return ($my_score <= $this->score);
				break;

			case self::EQ:
				return ($my_score == $this->score);
				break;
		}
	}
}

$scores = json_decode($_POST["scores"], true);

$db = Database::getInstance()->getDB();
$query = new Query(["lop" => $_POST['lop']]);
$laws_list = $db->executeQuery(Database::$class_laws, $query)->toArray();
$score_fields = $db->executeQuery(Database::$avail_fields, $query)->toArray();
$count = count($laws_list);
if ($count >= 1) {
    try {
    	$fields_count = array();
    	$s = 0;
    	$fields = $score_fields[0]->mon_hoc;
    	foreach ($fields as $field) {
    		$fields_count[$field] = 0;
    	}
    	foreach ($laws_list as $laws) {
			$k = true;
    		foreach ($laws->bo_luat as $law) {
    			$condition = new Condition(
    				$law->key,
    				$law->operator,
    				$law->score
    			);

    			foreach ($scores as $key => $score) {
    				if ($condition->subject == $key && !$condition->checkCondition($score)) {
    					$k = false;
    				}

    				if (!$k) break;
    			}

    			if ($k == true) {
					$fields_count[$laws->nang_khieu]++;
					$s++;
				}
    		}
    	}
    	foreach ($fields_count as $key => $value) {
			$fields_count[$key] = $s == 0 ? 0 : round($fields_count[$key] / $s, 3);
		}

		echo json_encode(["status" => 200, "result" => $s == 0 ? array() : $fields_count]);
    } catch (Exception $e) {
        echo json_encode(["status" => 503, "message" => $e->getMessage()]);
    } catch (TypeError $e) {
        echo json_encode(["status" => 503, "message" => $e->getMessage()]);
    }
    
} else {
    echo json_encode([
        "status" => 404,
        "message" => "Not found"
    ]);
}
?>