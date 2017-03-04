<?

$database_connect = mysqli_connect('localhost', 'root', 'root'); 
if (!$database_connect) { 
	header('HTTP/ 400 HOST ERROR', true, 400);
		
	$json_status = 'host not connected ' . mysqli_error($database_connect);
    $json_output[] = array('status' => $json_status, 'status_code' => 400);
	echo json_encode($json_output);
	exit;
	
} 

$database_table = mysqli_select_db($database_connect, "filmio");
if (!$database_table) { 
	header('HTTP/ 400 DATABASE ERROR', true, 400);
			
	$json_status = 'database table not found';
    $json_output[] = array('status' => $json_status, 'status_code' => 400);
	echo json_encode($json_output);
	exit;
	
}

$session_headers = $_SERVER;
$session_ip = $_SERVER['REMOTE_ADDR'];
$session_url =  $_SERVER["SERVER_NAME"] . reset(explode('?', $_SERVER["REQUEST_URI"]));
$session_page = str_replace(".php", "", basename($session_url));
$session_bearer = $session_headers["HTTP_FBEARER"];
$session_application = $session_headers["HTTP_FAPPKEY"];
$session_method = $_SERVER['REQUEST_METHOD'];

$auth_user = "twatboy";

?>