<?

include '../lib/auth.php';
include '../lib/dataobject.php';

$passed_limit = $_GET['limit'];
$passed_pagenation = $_GET['pangnation'];

if (empty($_GET['limit'])) $passed_limit = 40;
if (empty($_GET['pangnation'])) $passed_pagenation = 0;

$passed_pagenation = $data_passed_pagenation * $data_passed_limit;

if ($session_method == 'GET') {		
	$featured_query = mysqli_query($database_connect, "SELECT `featured_items` FROM `featured` WHERE `featured_title` LIKE 'taste'");
	$featured_items = explode(",", mysqli_fetch_assoc($featured_query)['featured_items']);
	
	$json_status = 'sections returned';
	$json_output[] = array('status' => $json_status, 'status_code' => 200, 'content' => $featured_items);
	echo json_encode($json_output);
	exit;
	
}
else {
	$json_status = $session_method . ' methods are not supported in the api';
	$json_output[] = array('status' => $json_status, 'status_code' => 405);
	echo json_encode($json_output);
	exit;
	
}

?>