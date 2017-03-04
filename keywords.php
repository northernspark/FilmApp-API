<?

include '../lib/auth.php';
include '../lib/keywords.php';

$passed_method = $_SERVER['REQUEST_METHOD'];
$passed_data = json_decode(file_get_contents('php://input'), true);
$passed_text = $passed_data['text'];
$passed_exclude = explode("," ,$passed_data['exclude']);

if (empty($passed_text)) {
	header('HTTP/ 422 MISSING DATA', true, 422);
		
	$json_status = 'input not passed';
    $json_output[] = array('status' => $json_status, 'error_code' => 422);
	echo json_encode($json_output);
	exit;
	
}
else {	
	$request_data = tags_stats();	
	$request_count = (int)$stats_data['request_count'] + 1;
	$request_limit = 500000;
	$request_output = $request_count . " requests of " . $request_limit;
	if ($request_count < $request_limit) {
		header('HTTP/ 200 OKAY', true, 200);
			
		$tags_exclude = array_push($passed_exclude, "colors");
		$tags_data = tags_produce($passed_text);
		$tags_relavant = $tags_data['relevant_tags'];
		foreach ($tags_relavant as $key => $value) {
			foreach ($value as $key => $tags) {
				//if ($key == "tag" && !in_array($tags, $passed_exclude)) 
					$tags_output[] = $tags;
							
			}
			
		}
		
		if (count($tags_output) == 0) $tags_output = array();
			
		$json_status = count($tags_output) . ' tags found';
	    $json_output[] = array('status' => $json_status, 'error_code' => 200, 'tags' => $tags_output, 'stats' => $tags_relavant);
		echo json_encode($json_output);
		exit;
		
	}
	else {
		header('HTTP/ 403 LIMIT REACHED', true, 403);
			
		$json_status = 'exceeded limit (' . $request_limit . ')';
	    $json_output[] = array('status' => $json_status, 'error_code' => 403);
		echo json_encode($json_output);
		exit;
		
	}

}

?>