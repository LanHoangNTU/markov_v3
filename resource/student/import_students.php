<?php 
	function remove_utf8_bom($text) {
	    $bom = pack('H*','EFBBBF');
	    $text = preg_replace("/^$bom/", '', $text);
	    return $text;
	}

	$array = array();

	if (isset($_FILES["upload"]) && !empty($_FILES["upload"]["name"])) {
		$target_dir = "../../uploads/";
		$file_name = basename($_FILES["upload"]["name"]);
		$target_file = $target_dir.$file_name;
		move_uploaded_file($_FILES["upload"]["tmp_name"], $target_file);
		$fp = fopen($target_file, "r");
		$header = explode(",", fgets($fp));
		for ($i=0; $i < count($header); $i++) { 
			$header[$i] = remove_utf8_bom(preg_replace('/\s+/', '', $header[$i]));
		}
		
		while(($line = fgets($fp)) != null){
			$sub_array = explode(",", $line);
			$sub_array_with_key = array();
			for ($i=0; $i < count($header); $i++) { 
				$sub_array_with_key[preg_replace('/\s+/', '', $header[$i])] = 
					($i > 0 
						? preg_replace('/\s+/', '', $sub_array[$i])
						: $sub_array[$i]
					);
			}
			$array[] = $sub_array_with_key;
		}

		fclose($fp);
	}

	$data_array = array();
	foreach ($array as $row) {
		$data = array();
		foreach ($header as $head) {
			$data[$head] = $row[$head];
		}

		$data_array[] = $data;
	}

	echo json_encode($data_array);
 ?>