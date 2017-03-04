<?

include '../lib/auth.php';
include '../lib/dataobject.php';

$passed_search = $_GET['search'];

if ($session_method == 'GET') {	
	if (empty($passed_search)) {
		$json_status = 'search parameter missing';
		$json_output[] = array('status' => $json_status, 'status_code' => 422);
		echo json_encode($json_output);
		exit;
		
	}
	else {
	
		
	}

	
}
else {
	$json_status = $session_method . ' methods are not supported in the api';
	$json_output[] = array('status' => $json_status, 'status_code' => 405);
	echo json_encode($json_output);
	exit;
	
}

?>